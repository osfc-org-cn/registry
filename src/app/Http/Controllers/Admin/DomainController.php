<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2019/4/14
 * Time: 16:42
 */

namespace App\Http\Controllers\Admin;


use App\Helper;
use App\Klsf\Dns\Helper as DnsHelper;
use App\Models\DnsConfig;
use App\Models\Domain;
use App\Models\DomainRecord;
use App\Models\User;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function post(Request $request)
    {
        $action = $request->post('action');
        switch ($action) {
            case 'domainList':
                return $this->domainList($request);
            case 'update':
                return $this->update($request);
            case 'add':
                return $this->add($request);
            case 'select':
                return $this->select($request);
            case 'delete':
                return $this->delete($request);
            default:
                return ['status' => -1, 'message' => '对不起，此操作不存在！'];
        }
    }

    private function domainList(Request $request)
    {
        $result = ['status' => -1];
        $dns = $request->post('dns');
        if (!$dns) {
            $result['message'] = '请选择域名解析平台';
        } elseif (!$config = DnsConfig::find($dns)) {
            $result['message'] = '请先对此解析平台进行配置';
        } elseif (!$_dns = DnsHelper::getModel($dns)) {
            $result['message'] = '暂不支持此域名解析平台';
        } else {
            $_dns->config($config->config);
            list($list, $error) = $_dns->getDomainList();
            if ($list) {
                $data = [];
                foreach ($list as $domain) {
                    if (!Domain::where('domain', $domain['Domain'])->first()) {
                        $data[] = [
                            'domain' => $domain['Domain'],
                            'domain_id' => $domain['DomainId']
                        ];
                    }
                }
                $result = ['status' => 0, 'message' => '保存成功', 'data' => $data];
            } else {
                $result['message'] = $error;
            }
        }
        return $result;
    }

    private function update(Request $request)
    {
        $result = ['status' => -1];
        $did = intval($request->post('did'));
        $groups = $request->post('groups');
        $point = abs(intval($request->post('point')));
        $desc = $request->post('desc');

        if (!$did || !$row = Domain::find($did)) {
            $result['message'] = '域名不存在';
        } elseif (empty($groups)) {
            $result['message'] = '请选择用户组';
        } else {
            if (in_array('0', $groups)) {
                $groups = ["0"];
            }
            $row->groups = implode(',', $groups);
            $row->point = $point;
            $row->desc = $desc;
            if ($row->save()) {
                $result = ['status' => 0, 'message' => '修改成功'];
            } else {
                $result['message'] = '修改失败，或未做任何修改！';
            }
        }
        return $result;
    }

    private function add(Request $request)
    {
        $result = ['status' => -1];
        $dns = $request->post('dns');
        $domain = $request->post('domain');
        $domain = explode(',', trim($domain));
        $desc = $request->post('desc');
        $groups = $request->post('groups');
        $point = abs(intval($request->post('point')));

        if (!$dns) {
            $result['message'] = '请选择域名解析平台';
        } elseif (count($domain) != 2 || !$domain[0]) {
            $result['message'] = '请选择要添加的域名';
        } elseif (!$config = DnsConfig::find($dns)) {
            $result['message'] = '请先对此解析平台进行配置';
        } elseif (!$_dns = DnsHelper::getModel($dns)) {
            $result['message'] = '暂不支持此域名解析平台';
        } elseif (empty($groups)) {
            $result['message'] = '请选择用户组';
        } else {
            $_dns->config($config->config);
            list($list, $error) = $_dns->getDomainRecords($domain[0], $domain[1]);
            if ($list !== false) {
                if (in_array('0', $groups)) {
                    $groups = ["0"];
                }
                if (Domain::where('dns', $dns)->where('domain_id', $domain[0])->first()) {
                    $result['message'] = '此域名已经添加过';
                } elseif (Domain::create([
                    'domain' => $domain[1],
                    'domain_id' => $domain[0],
                    'dns' => $dns,
                    'groups' => implode(',', $groups),
                    'desc' => $desc,
                    'point' => $point
                ])) {
                    $result = ['status' => 0, 'message' => '添加成功'];
                } else {
                    $result['message'] = '添加失败，请稍后再试！';
                }
            } else {
                $result['message'] = '添加失败，获取域名信息失败！' . $error;
            }
        }
        return $result;
    }

    private function select(Request $request)
    {
        $data = Domain::orderBy('did', 'desc')->pageSelect();
        return ['status' => 0, 'message' => '', 'data' => $data];
    }

    private function delete(Request $request)
    {
        $result = ['status' => -1];
        $id = intval($request->post('id'));
        if (!$id || !$row = Domain::find($id)) {
            $result['message'] = '域名不存在';
        } elseif ($row->delete()) {
            // 获取与域名关联的所有记录
            $records = DomainRecord::where('did', $id)->get();
            
            // 记录用户ID集合，用于后续发送邮件
            $userIds = [];
            
            // 为每个用户收集记录详情
            $userRecords = [];
            
            // 删除所有关联记录
            foreach ($records as $record) {
                // 如果记录有关联用户，添加到集合中
                if ($record->uid) {
                    $userId = $record->uid;
                    
                    // 初始化用户记录数组（如果不存在）
                    if (!isset($userRecords[$userId])) {
                        $userRecords[$userId] = [];
                    }
                    
                    // 添加记录详情
                    $userRecords[$userId][] = [
                        'name' => $record->name,
                        'type' => $record->type,
                        'value' => $record->value,
                        'created_at' => $record->created_at->format('Y-m-d H:i:s')
                    ];
                    
                    $userIds[$userId] = [
                        'name' => $record->name,
                        'domain' => $row->domain
                    ];
                }
                
                // 删除记录
                Helper::deleteRecord($record);
                $record->delete();
            }
            
            // 向所有受影响的用户发送邮件通知
            foreach ($userIds as $userId => $domainInfo) {
                $user = User::find($userId);
                if ($user && $user->email) {
                    $domainName = $domainInfo['name'] . '.' . $domainInfo['domain'];
                    
                    // 发送域名删除通知邮件
                    Helper::sendEmail(
                        $user->email,
                        'Domain Deletion Notice',
                        'email.domain-deleted',
                        [
                            'username' => $user->username,
                            'domainName' => $domainName,
                            'webName' => config('sys.web.name', 'OSFC Registry'),
                            'reason' => $request->post('reason'), // 可选的删除原因
                            'records' => $userRecords[$userId] // 添加用户的所有记录详情
                        ]
                    );
                }
            }
            
            // 删除所有域名记录
            DomainRecord::where('did', $id)->delete();
            $result = ['status' => 0, 'message' => '删除成功'];
        } else {
            $result['message'] = '删除失败，请稍后再试！';
        }
        return $result;
    }

}