<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2019/4/14
 * Time: 16:41
 */

namespace App\Http\Controllers\Home;


use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\DomainRecord;
use App\Models\User;
use App\Models\UserPointRecord;
use App\Models\Invitation;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function post(Request $request)
    {
        $action = $request->post('action');
        switch ($action) {
            case 'verify':
                return $this->verify($request);
            case 'profile':
                return $this->profile($request);
            case 'recordList':
                return $this->recordList($request);
            case 'domainList':
                return $this->domainList($request);
            case 'pointRecord':
                return $this->pointRecord($request);
            case 'inviteList':
                return $this->inviteList($request);
        }
        if (Auth::user()->status != 2) {
            return ['status' => -1, 'message' => "Sorry, please complete the certification first! <a href='/home/profile'>Click to certify</a>"];
        }

        switch ($action) {
            case 'recordStore':
                return $this->recordStore($request);
            case 'recordDelete':
                return $this->recordDelete($request);
            default:
                return ['status' => -1, 'message' => 'Sorry, this operation does not exist!'];
        }
    }

    private function verify(Request $request)
    {
        $result = ['status' => -1];
        $user = Auth::user();
        if ($user->status != 1) {
            $result['message'] = 'Current status does not need certification';
        } else {
            list($ret, $error) = Helper::sendVerifyEmail($user);
            if ($ret) {
                $result = ['status' => 0, 'message' => "The certification email has been sent to {$user->email}, please check it."];
            } else {
                $result['message'] = "Failed to send email: " . $error;
            }
        }
        return $result;
    }

    private function profile(Request $request)
    {
        $result = ['status' => -1];
        $old_password = $request->post('old_password');
        $new_password = $request->post('new_password');
        if (strlen($old_password) < 5) {
            $result['message'] = 'Old password verification failed';
        } elseif (!Hash::check($old_password, Auth::user()->password)) {
            $result['message'] = 'Old password verification failed';
        } elseif (strlen($new_password) < 5) {
            $result['message'] = 'New password is too simple';
        } else {
            if (User::where('uid', Auth::id())->update([
                'password' => Hash::make($new_password),
                'sid' => md5(uniqid() . Str::random())
            ])) {
                $result = ['status' => 0, 'message' => 'Modification successful'];
            } else {
                $result['message'] = 'Failed to modify, please try again later!';
            }
        }
        return $result;
    }

    private function pointRecord(Request $request)
    {
        $data = UserPointRecord::search()->where('uid', Auth::id())->orderBy('id', 'desc')->pageSelect();
        return ['status' => 0, 'message' => '', 'data' => $data];
    }

    private function recordStore(Request $request)
    {
        $result = ['status' => -1];
        $id = intval($request->post('id'));
        $data = [
            'uid' => Auth::id(),
            'did' => intval($request->post('did')),
            'name' => $request->post('name'),
            'type' => $request->post('type'),
            'line_id' => $request->post('line_id'),
            'value' => $request->post('value'),
            'line' => '默认'
        ];
        
        // 处理MX和SRV记录的特殊参数
        $mx_priority = null;
        $srv_params = null;
        
        if ($data['type'] === 'MX') {
            $mx_priority = (int)$request->post('mx_priority', 10);
            // 如果没有提供优先级，设置默认值为10
            if ($mx_priority < 1) {
                $mx_priority = 10;
            }
            // 保存到数据库记录中，用于显示
            $data['mx_priority'] = $mx_priority;
        } elseif ($data['type'] === 'SRV') {
            // 获取SRV特定参数
            $srv_priority = (int)$request->post('srv_priority', 0);
            $srv_weight = (int)$request->post('srv_weight', 5);
            $srv_port = (int)$request->post('srv_port', 443);
            
            // 验证参数
            if ($srv_priority < 0 || $srv_priority > 65535) {
                $result['message'] = 'SRV Priority must be between 0 and 65535';
                return $result;
            }
            if ($srv_weight < 0 || $srv_weight > 65535) {
                $result['message'] = 'SRV Weight must be between 0 and 65535';
                return $result;
            }
            if ($srv_port < 1 || $srv_port > 65535) {
                $result['message'] = 'SRV Port must be between 1 and 65535';
                return $result;
            }
            
            // 验证SRV记录的名称格式
            if (!preg_match('/^_[a-z0-9\-]+\._[a-z0-9\-]+/', $data['name'])) {
                $result['message'] = 'SRV record name must be in format: _service._protocol (e.g. _sip._tcp)';
                return $result;
            }
            
            // 验证目标值
            if (!filter_var($data['value'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                $result['message'] = 'SRV target must be a valid hostname (e.g. sip.example.com)';
                return $result;
            }
            
            // 确保目标主机名没有以点结尾
            $data['value'] = rtrim($data['value'], '.');
            
            // 按照SRV记录格式存储这些值：优先级 权重 端口 目标
            // 将这些参数存储到数据库中
            $data['srv_priority'] = $srv_priority;
            $data['srv_weight'] = $srv_weight;
            $data['srv_port'] = $srv_port;
            
            // 有些DNS服务商需要组合成标准格式，有些则需要分别提供
            $srv_params = [
                'priority' => $srv_priority,
                'weight' => $srv_weight,
                'port' => $srv_port,
                'target' => $data['value']
            ];
            
            // 对于需要组合值的DNS服务商，格式化SRV值
            // 标准格式：[优先级] [权重] [端口] [目标主机]
            // 不修改原始值，因为不同DNS提供商可能有不同格式要求
        }
        
        list($check, $error) = Helper::checkDomainName($data['name']);
        if (!$check) {
            $result['message'] = $error;
        } elseif ($id && !$record = DomainRecord::where('uid', Auth::id())->where('id', $id)->first()) {
            $result['message'] = 'Record does not exist';
        } elseif (!$data['value']) {
            $result['message'] = 'Please enter the record value';
        } elseif (!$id && DomainRecord::where('did', $data['did'])->where('name', $data['name'])->where('uid', '!=', Auth::id())->where('line_id', $data['line_id'])->first()) {
            $result['message'] = 'This host record has been used';
        } elseif (!$domain = Domain::available()->where('did', $data['did'])->first()) {
            $result['message'] = 'Domain does not exist, or no permission';
        } elseif (!$dns = $domain->dnsConfig) {
            $result['message'] = 'Domain configuration error [No Config]';
        } elseif (!$_dns = \App\Klsf\Dns\Helper::getModel($dns->dns)) {
            $result['message'] = 'Domain configuration error [Unsupported]';
        } else {
            // 检测 IP 是否合规
            $checkIpResult = Helper::checkIPValidity($data['type'], $data['value']);
            if ($checkIpResult !== true) {
                $result['message'] = $checkIpResult;
                return $result;
            }
            
            $_dns->config($dns->config);
            $lines = $_dns->getRecordLine($domain->domain_id, $domain->domain);
            foreach ($lines as $line) {
                if ($line['Id'] == $data['line_id']) {
                    $data['line'] = $line['Name'];
                }
            }
            if ($id) {
                //更新
                // 根据记录类型处理特殊参数
                if ($data['type'] === 'MX') {
                    // 针对MX记录，添加优先级参数
                    list($ret, $error) = $_dns->updateDomainRecord(
                        $record->record_id, 
                        $data['name'], 
                        $data['type'], 
                        $data['value'], 
                        $data['line_id'],
                        $domain->domain_id, 
                        $domain->domain,
                        ['mx' => $mx_priority]
                    );
                } elseif ($data['type'] === 'SRV' && $srv_params) {
                    // 针对SRV记录，添加额外参数
                    list($ret, $error) = $_dns->updateDomainRecord(
                        $record->record_id, 
                        $data['name'], 
                        $data['type'], 
                        $data['value'], 
                        $data['line_id'],
                        $domain->domain_id, 
                        $domain->domain,
                        $srv_params
                    );
                } else {
                    // 常规记录更新
                    list($ret, $error) = $_dns->updateDomainRecord(
                        $record->record_id, 
                        $data['name'], 
                        $data['type'], 
                        $data['value'], 
                        $data['line_id'],
                        $domain->domain_id, 
                        $domain->domain
                    );
                }
                
                if ($ret) {
                    if (DomainRecord::where('id', $id)->update($data)) {
                        $result = ['status' => 0, 'message' => 'Update successful'];
                    } else {
                        $result['message'] = 'Failed to update, please try again later!';
                    }
                } else {
                    $result['message'] = 'Failed to update record: ' . $error;
                }
            } else {
                //添加
                // 检查是否是二级域名
                $isSubdomain = strpos($data['name'], '.') !== false && $data['type'] !== 'SRV';
                $pointNeeded = $domain->point;
                
                // 如果是二级域名，需要额外的积分
                if ($isSubdomain) {
                    $subdomainPoint = (int)config('sys.subdomain_point', 0);
                    $pointNeeded += $subdomainPoint;
                }
                
                if ($pointNeeded > 0 && Auth::user()->point < $pointNeeded) {
                    $result['message'] = 'Insufficient account balance';
                } else {
                    // 根据记录类型处理特殊参数
                    if ($data['type'] === 'MX') {
                        // 针对MX记录，添加优先级参数
                        list($ret, $error) = $_dns->addDomainRecord(
                            $data['name'], 
                            $data['type'], 
                            $data['value'], 
                            $data['line_id'],
                            $domain->domain_id, 
                            $domain->domain,
                            ['mx' => $mx_priority]
                        );
                    } elseif ($data['type'] === 'SRV' && $srv_params) {
                        // 针对SRV记录，添加额外参数
                        list($ret, $error) = $_dns->addDomainRecord(
                            $data['name'], 
                            $data['type'], 
                            $data['value'], 
                            $data['line_id'],
                            $domain->domain_id, 
                            $domain->domain,
                            $srv_params
                        );
                    } else {
                        // 常规记录添加
                        list($ret, $error) = $_dns->addDomainRecord(
                            $data['name'], 
                            $data['type'], 
                            $data['value'], 
                            $data['line_id'],
                            $domain->domain_id, 
                            $domain->domain
                        );
                    }
                    
                    if ($ret) {
                        // 处理积分扣除
                        $pointDeducted = false;
                        $pointMessage = "Add record [{$data['name']}.{$domain->domain}]({$data['line']})";
                        
                        // 如果需要扣除积分
                        if ($pointNeeded > 0) {
                            $pointDeducted = User::point(Auth::id(), 'consume', 0 - $pointNeeded, $pointMessage);
                            
                            if (!$pointDeducted) {
                                $result['message'] = 'Insufficient account balance';
                                $_dns->deleteDomainRecord($ret['RecordId'], $domain->domain_id, $domain->domain);
                                return $result;
                            }
                        }
                        
                        $data['record_id'] = $ret['RecordId'];
                        
                        // 添加重试机制，最多尝试3次
                        $maxRetries = 3;
                        $retryCount = 0;
                        $dbSuccess = false;
                        
                        while ($retryCount < $maxRetries && !$dbSuccess) {
                            try {
                                // 使用事务确保数据一致性
                                \Illuminate\Support\Facades\DB::beginTransaction();
                                
                                // 创建记录
                                $record = DomainRecord::create($data);
                                
                                if ($record) {
                                    $dbSuccess = true;
                                    \Illuminate\Support\Facades\DB::commit();
                                    $result = ['status' => 0, 'message' => 'Add successful'];
                                } else {
                                    \Illuminate\Support\Facades\DB::rollBack();
                                    $retryCount++;
                                    // 短暂延迟后重试
                                    usleep(100000); // 100ms
                                }
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\DB::rollBack();
                                \Illuminate\Support\Facades\Log::error('Failed to create record in database: ' . $e->getMessage());
                                $retryCount++;
                                // 短暂延迟后重试
                                usleep(100000); // 100ms
                            }
                        }
                        
                        // 如果数据库操作失败
                        if (!$dbSuccess) {
                            // 如果创建记录失败但已扣除积分，需要退还积分
                            if ($pointNeeded > 0 && $pointDeducted) {
                                User::point(Auth::id(), 'refund', $pointNeeded, "Refund for failed record creation");
                            }
                            
                            // 尝试删除DNS记录，但不依赖其成功与否
                            try {
                                $_dns->deleteDomainRecord($ret['RecordId'], $domain->domain_id, $domain->domain);
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('Failed to delete DNS record after database failure: ' . $e->getMessage());
                                // 记录错误但继续执行
                            }
                            
                            // 添加同步修复任务到队列（如果有队列系统）
                            // 这里可以添加一个队列任务，稍后尝试修复不一致的记录
                            
                            $result['message'] = 'Failed to add record to database. Please contact administrator if DNS record was created but not showing in your account.';
                        }
                    } else {
                        $result['message'] = 'Failed to add record: ' . $error;
                    }
                }
            }
        }
        return $result;
    }

    private function recordList(Request $request)
    {
        $data = DomainRecord::search()
            ->where('uid', auth()->id())
            ->orderBy('id', 'desc')
            ->pageSelect();
        return ['status' => 0, 'message' => '', 'data' => $data];
    }

    private function domainList(Request $request)
    {
        $data = Domain::with('dnsConfig')->available()->get();
        $list = [];
        foreach ($data as $domain) {
            if ($dns = $domain->dnsConfig) {
                if ($_dns = \App\Klsf\Dns\Helper::getModel($dns->dns)) {
                    $_dns->config($dns->config);
                    $list[] = [
                        'did' => $domain->did,
                        'domain' => $domain->domain,
                        'point' => $domain->point,
                        'desc' => $domain->desc,
                        'line' => $_dns->getRecordLine()
                    ];
                }
            }
        }
        return ['status' => 0, 'message' => '', 'data' => $list];
    }

    private function recordDelete(Request $request)
    {
        $result = ['status' => -1];
        $id = intval($request->post('id'));
        if (!$id || !$row = DomainRecord::where('id', $id)->where('uid', Auth::id())->first()) {
            $result['message'] = 'Record does not exist';
        } else {
            // 获取与记录关联的域名
            $domain = $row->domain;
            
            // 先从DNS删除记录
            $dnsDeleted = false;
            $dnsError = '';
            
            try {
                $dnsDeleted = Helper::deleteRecord($row);
                if (!$dnsDeleted) {
                    // DNS删除失败，记录错误但继续尝试删除数据库记录
                    \Illuminate\Support\Facades\Log::warning("Failed to delete DNS record ID: {$row->record_id}, but will continue to delete database record");
                }
            } catch (\Exception $e) {
                $dnsError = $e->getMessage();
                \Illuminate\Support\Facades\Log::error("Exception when deleting DNS record: {$dnsError}");
                // 继续尝试删除数据库记录
            }
            
            // 从数据库删除记录
            $dbDeleted = false;
            $maxRetries = 3;
            $retryCount = 0;
            
            while ($retryCount < $maxRetries && !$dbDeleted) {
                try {
                    \Illuminate\Support\Facades\DB::beginTransaction();
                    
                    if ($row->delete()) {
                        $dbDeleted = true;
                        \Illuminate\Support\Facades\DB::commit();
                        
                        // 如果域名存在并且有积分需要退还
                        if ($domain) {
                            $pointToRefund = $domain->point;
                            
                            // 检查是否是二级域名
                            $isSubdomain = strpos($row->name, '.') !== false;
                            if ($isSubdomain) {
                                // 如果是二级域名，额外退还二级域名所需积分
                                $subdomainPoint = (int)config('sys.subdomain_point', 0);
                                $pointToRefund += $subdomainPoint;
                            }
                            
                            // 如果有积分需要退还，执行退还操作
                            if ($pointToRefund > 0) {
                                User::point(Auth::id(), 'refund', $pointToRefund, "Refund for deleted record [{$row->name}.{$domain->domain}]({$row->line})");
                            }
                        }
                        
                        $result = ['status' => 0, 'message' => 'Delete successful'];
                    } else {
                        \Illuminate\Support\Facades\DB::rollBack();
                        $retryCount++;
                        usleep(100000); // 100ms
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\DB::rollBack();
                    \Illuminate\Support\Facades\Log::error("Failed to delete database record: " . $e->getMessage());
                    $retryCount++;
                    usleep(100000); // 100ms
                }
            }
            
            // 如果数据库删除失败但DNS删除成功，记录不一致情况
            if (!$dbDeleted && $dnsDeleted) {
                \Illuminate\Support\Facades\Log::alert("Inconsistency detected: DNS record deleted but database record remains for ID: {$id}");
                $result['message'] = 'Failed to delete from database, but DNS record may have been removed. Please contact administrator.';
            } 
            // 如果两者都失败
            else if (!$dbDeleted && !$dnsDeleted) {
                $result['message'] = 'Failed to delete, please try again later!';
            }
        }
        return $result;
    }

    /**
     * 处理邀请列表请求
     */
    protected function inviteList(Request $request)
    {
        $query = Invitation::with('invitee')
            ->where('inviter_uid', Auth::id())
            ->orderBy('id', 'desc');
            
        $data = $query->paginate(10);
        
        return response()->json([
            'status' => 0,
            'message' => 'success',
            'data' => $data
        ]);
    }
}