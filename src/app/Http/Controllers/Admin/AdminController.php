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
}