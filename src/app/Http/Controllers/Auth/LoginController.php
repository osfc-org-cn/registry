<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    protected $guardName = 'web';

    use AuthenticatesUsers;

    protected function guard()
    {
        return auth()->guard($this->guardName);
    }

    public function username()
    {
        return 'username';
    }

    protected function sendLoginResponse(Request $request)
    {
        $user = $this->guard()->user();
        if ($user->status == 0) {
            return ['status' => -1, 'message' => 'Sorry, the account has been disabled!'];
        } elseif ($this->guardName != 'admin' && $user->gid == 99) {
            return ['status' => -1, 'message' => 'Account or password is incorrect'];
        } else {
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            $this->authenticated($request, $user);
            $key = 'login_user_sid_' . $this->guardName;
            $request->session()->put([
                $key => $user->sid,
            ]);

            return ['status' => 0, 'message' => 'Login successful!', 'go' => $this->guardName == 'admin' ? '/admin' : '/home'];
        }
    }

    protected function credentials(Request $request)
    {
        $data = [
            'username' => $request->post('username'),
            'password' => $request->post('password'),
        ];
        if ($this->guardName === 'admin') {
            $data['gid'] = 99;
        }
        return $data;
    }

    public function userLogin(Request $request)
    {
        if ($request->method() === 'POST') {
            $this->guardName = 'web';
            try {
                return $this->login($request);
            } catch (\Exception $e) {
                return ['status' => -1, 'message' => 'Account or password is incorrect'];
            }
        } else {
            return view('login');
        }
    }

    public function adminLogin(Request $request)
    {
        if ($request->method() === 'POST') {
            if (strtolower($request->post('code')) !== Session::get('captcha_code')) {
                return ['status' => -1, 'message' => 'Incorrect verification code'];
            }
            $this->guardName = 'admin';
            try {
                return $this->login($request);
            } catch (\Exception $e) {
                return ['status' => -1, 'message' => 'Account or password is incorrect'];
            }
        } else {
            return view('admin.login');
        }
    }

    public function adminLogout(Request $request)
    {
        if (auth('admin')->check()) {
            User::where('uid', auth('admin')->id())->update(['sid' => md5(Str::random())]);
        }
        auth('admin')->logout();
        return redirect('/admin/login');
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            User::where('uid', auth()->id())->update(['sid' => md5(Str::random())]);
        }
        auth()->logout();
        return redirect('/login');
    }
}
