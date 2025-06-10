<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    // ... 原来的代码 ...
    
    /**
     * 显示注册页面
     */
    public function showRegistrationForm()
    {
        // 获取邀请码
        $invite = request()->query('invite');
        
        return view('auth.register', compact('invite'));
    }
    
    /**
     * 创建新用户
     */
    protected function create(array $data)
    {
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => config('sys.user.email') ? 1 : 2,
            'point' => config('sys.user.point', 0),
        ]);
        
        // 处理邀请关系
        if (!empty($data['invite'])) {
            $inviter = User::where('uid', $data['invite'])->first();
            if ($inviter) {
                Invitation::create([
                    'inviter_uid' => $inviter->uid,
                    'invitee_uid' => $user->uid,
                    'status' => 0 // 未验证
                ]);
            }
        }
        
        return $user;
    }
    
    /**
     * 验证表单数据
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invite' => ['nullable', 'exists:users,uid']
        ]);
    }
} 