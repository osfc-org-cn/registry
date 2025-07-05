<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2019/4/14
 * Time: 16:41
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function post(Request $request)
    {
        $action = $request->post('action');
        switch ($action) {
            case 'profile':
                return $this->profile($request);
            default:
                return ['status' => -1, 'message' => '对不起，此操作不存在！'];
        }
    }

    private function profile(Request $request)
    {
        $result = ['status' => -1];
        $old_password = $request->post('old_password');
        $new_password = $request->post('new_password');
        if (strlen($old_password) < 5) {
            $result['message'] = '旧密码验证失败';
        } elseif (!Hash::check($old_password, Auth::guard('admin')->user()->password)) {
            $result['message'] = '旧密码验证失败';
        } elseif (strlen($new_password) < 5) {
            $result['message'] = '新密码太简单';
        } else {
            if (User::where('uid', Auth::guard('admin')->id())->update([
                'password' => Hash::make($new_password),
                'sid' => md5(uniqid() . Str::random())
            ])) {
                $result = ['status' => 0, 'message' => '修改成功'];
            } else {
                $result['message'] = '修改失败，请稍后再试！';
            }
        }
        return $result;
    }
    
    /**
     * 显示邮件模板预览页面
     */
    public function testEmailView(Request $request)
    {
        $template = $request->get('template', 'verify');
        $data = [];
        
        // 准备测试数据
        switch ($template) {
            case 'verify':
                $data = [
                    'username' => '测试用户',
                    'webName' => config('sys.web.name', 'OSFC Registry'),
                    'url' => 'https://registry.osfc.org.cn/verify?code=test_code_here'
                ];
                break;
            case 'password':
                $data = [
                    'username' => '测试用户',
                    'webName' => config('sys.web.name', 'OSFC Registry'),
                    'url' => 'https://registry.osfc.org.cn/password?code=test_code_here'
                ];
                break;
            case 'user-banned':
                $data = [
                    'username' => '测试用户',
                    'webName' => config('sys.web.name', 'OSFC Registry')
                ];
                break;
            case 'user-deleted':
                $data = [
                    'username' => '测试用户',
                    'webName' => config('sys.web.name', 'OSFC Registry')
                ];
                break;
            case 'domain-deleted':
                $data = [
                    'username' => '测试用户',
                    'domainName' => 'example.osfc.org.cn',
                    'webName' => config('sys.web.name', 'OSFC Registry'),
                    'reason' => 'Routine cleanup',
                    'record' => [
                        'name' => 'example',
                        'type' => 'A',
                        'value' => '192.168.1.1',
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                ];
                break;
            case 'test':
                $data = [];
                break;
        }
        
        return view('email.' . $template, $data);
    }
    
    /**
     * 显示邮件测试索引页面
     */
    public function testEmailIndex()
    {
        return view('admin.email-test');
    }
    
    /**
     * 验证API密钥
     * 
     * @param Request $request
     * @return bool|array 验证成功返回true，失败返回错误响应数组
     */
    private function validateApiKey(Request $request)
    {
        // 从请求头或GET参数获取API密钥
        $apiKey = $request->header('X-API-KEY') ?? $request->query('api_key');
        $configApiKey = config('sys.api.key', '');
        
        // 如果未配置API密钥或密钥不匹配，返回401错误
        if (empty($configApiKey) || $apiKey !== $configApiKey) {
            return [
                'status' => 401,
                'message' => 'Unauthorized: Invalid API Key',
                'data' => null
            ];
        }
        
        return true;
    }
    
    /**
     * API: 获取所有域名信息
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllDomains(Request $request)
    {
        // 验证API密钥
        $validation = $this->validateApiKey($request);
        if ($validation !== true) {
            return response()->json($validation, 401);
        }
        
        // 获取筛选参数
        $recordType = $request->query('type'); // 记录类型筛选 (A, CNAME, MX等)
        $did = $request->query('did'); // 特定域名ID筛选
        $uid = $request->query('uid'); // 特定用户ID筛选
        $recordId = $request->query('record_id'); // 特定记录ID筛选
        
        // 获取域名数据
        $domainsQuery = \App\Models\Domain::query();
        
        // 如果指定了域名ID，只查询该域名
        if ($did) {
            $domainsQuery->where('did', $did);
        }
        
        // 获取域名及其记录
        $domains = $domainsQuery->with(['records' => function($query) use ($recordType, $uid, $recordId) {
            $query->select('id', 'did', 'uid', 'name', 'type', 'value', 'line', 'created_at', 'updated_at');
            
            // 根据记录类型筛选
            if ($recordType) {
                $query->where('type', $recordType);
            }
            
            // 根据用户ID筛选
            if ($uid) {
                $query->where('uid', $uid);
            }
            
            // 根据记录ID筛选
            if ($recordId) {
                $query->where('id', $recordId);
            }
        }])->get([
            'did', 'domain', 'domain_id', 'dns', 'groups', 'point', 'desc', 'created_at', 'updated_at'
        ]);
        
        // 为每个记录添加完整域名
        foreach ($domains as $domain) {
            foreach ($domain->records as $record) {
                // 添加完整域名字段
                $record->allname = $record->name . '.' . $domain->domain;
            }
        }
        
        // 如果只需要特定记录ID的数据
        if ($recordId) {
            $foundRecord = null;
            foreach ($domains as $domain) {
                foreach ($domain->records as $record) {
                    if ($record->id == $recordId) {
                        $foundRecord = $record;
                        break 2;
                    }
                }
            }
            
            if ($foundRecord) {
                return response()->json([
                    'status' => 0,
                    'message' => 'success',
                    'data' => $foundRecord
                ]);
            }
        }
        
        return response()->json([
            'status' => 0,
            'message' => 'success',
            'data' => $domains
        ]);
    }
    
    /**
     * API: 获取所有用户信息
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers(Request $request)
    {
        // 验证API密钥
        $validation = $this->validateApiKey($request);
        if ($validation !== true) {
            return response()->json($validation, 401);
        }
        
        // 获取筛选参数
        $uid = $request->query('uid'); // 特定用户ID筛选
        $gid = $request->query('gid'); // 特定用户组ID筛选
        $status = $request->query('status'); // 用户状态筛选
        $email = $request->query('email'); // 邮箱筛选
        $username = $request->query('username'); // 用户名筛选
        
        // 构建查询
        $usersQuery = \App\Models\User::with(['group']);
        
        // 排除管理员账户
        $usersQuery->where('gid', '!=', 99);
        
        // 应用筛选条件
        if ($uid) {
            $usersQuery->where('uid', $uid);
        }
        
        if ($gid) {
            $usersQuery->where('gid', $gid);
        }
        
        if ($status !== null && $status !== '') {
            $usersQuery->where('status', $status);
        }
        
        if ($email) {
            $usersQuery->where('email', 'like', "%{$email}%");
        }
        
        if ($username) {
            $usersQuery->where('username', 'like', "%{$username}%");
        }
        
        // 获取用户数据
        $users = $usersQuery->get([
            'uid', 'username', 'email', 'gid', 'point', 'status', 'created_at', 'updated_at'
        ]);
        
        // 如果是查询单个用户
        if ($uid && $users->count() === 1) {
            return response()->json([
                'status' => 0,
                'message' => 'success',
                'data' => $users->first()
            ]);
        }
        
        return response()->json([
            'status' => 0,
            'message' => 'success',
            'data' => $users
        ]);
    }
}