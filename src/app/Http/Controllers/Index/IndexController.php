<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2019/4/14
 * Time: 16:41
 */

namespace App\Http\Controllers\Index;


use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\DomainRecord;
use App\Models\Invitation;
use App\Models\User;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class IndexController extends Controller
{
    public function autoCheck(Request $request, $key)
    {
        if (strlen($key) != 32 || config('sys.cronKey') !== $key) {
            exit('监控密匙不正确！');
        } else {
            $start = time();
            $keywords = config('sys.keywords');
            $keywords = explode("
", $keywords);
            $_keywords = [];
            foreach ($keywords as $k) {
                $k = trim($k);
                if (strlen($k) > 1) {
                    $_keywords[] = $k;
                }
            }
            if (empty($_keywords)) {
                exit('未配置检测关键词！');
            }

            $client = new Client([
                'timeout' => 10,
                'http_errors' => false,
                'verify' => false,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36'
                ]
            ]);
            echo "开始检测----" . date("Y-m-d H:i:s") . "<br>\r\n";
            while ($record = DomainRecord::with('domain')->where('checked_at', '<', $start - 300)->orderBy('checked_at', 'asc')->first()) {
                $record->checked_at = time();
                $record->save();
                $del = false;

                if (!$record->domain) {
                    echo "{$record->id}----域名不存在<br>\r\n";
                } else {
                    $domain = $record->name . '.' . $record->domain->domain;
                    try {
                        $res = $client->get('http://' . $domain);
                        $body = (string)$res->getBody();
                        foreach ($_keywords as $k) {
                            if (strpos($body, $k) > -1) {
                                //包含关键词，直接删除
                                Helper::deleteRecord($record);
                                echo "{$record->id}----{$domain}----del:{$k}<br>\r\n";
                                $record->delete();
                                $del = true;
                                break;
                            }
                        }
                        if (!$del) {
                            echo "{$record->id}----{$domain}----ok<br>\r\n";
                        }
                    } catch (\Exception $e) {
                        echo "{$record->id}----{$domain}----{$e->getMessage()}<br>\r\n";
                    }
                }

                if (time() - $start > 25) {
                    break;
                }
            }
        }

    }

    public function password(Request $request)
    {
        if ($request->method() === 'POST') {
            $action = $request->post('action');
            switch ($action) {
                case 'sendPasswordEmail':
                    return $this->sendPasswordEmail($request);
                case 'setPassword':
                    return $this->setPassword($request);
                default:
                    return ['status' => -1, 'message' => 'Sorry, this operation does not exist!'];
            }
        } else {
            $code = $request->get('code');
            if ($sid = Crypt::decrypt($code)) {
                if ($user = User::where('sid', $sid)->first()) {
                    return view('password')->with('user', $user);
                }
            }
            abort(500, 'Link expired@/login');
        }
    }

    private function setPassword(Request $request)
    {
        $result = ['status' => 1];
        $code = $request->get('code');
        $password = $request->post('password');
        $re_password = $request->post('re_password');
        if (!$sid = Crypt::decrypt($code)) {
            $result['message'] = 'Link expired';
        } elseif (!$user = User::where('sid', $sid)->first()) {
            $result['message'] = 'Link expired';
        } elseif (strlen($password) < 5) {
            $result['message'] = 'New password is too simple';
        } elseif ($re_password !== $password) {
            $result['message'] = 'The two passwords do not match';
        } else {
            $user->sid = md5(uniqid() . Str::random());
            $user->password = Hash::make($password);
            if ($user->save()) {
                $result = ['status' => 0, 'message' => "Password reset successful!"];
            } else {
                $result['message'] = 'Failed to reset password, please try again later!';
            }
        }
        return $result;
    }

    private function sendPasswordEmail(Request $request)
    {
        $result = ['status' => 1];
        $username = $request->post('username');
        if (strlen($username) < 3) {
            $result['message'] = 'Please enter the account or email address you want to find';
        } elseif (strtolower($request->post('code')) !== Session::get('captcha_code')) {
            $result['message'] = 'Incorrect verification code';
        } elseif (!$user = User::where('gid', '>', 99)->where(function ($query) use ($username) {
            $query->where('username', $username)->orWhere('email', $username);
        })->first()) {
            $result['message'] = 'Account or email address does not exist';
        } else {
            $url = "http://{$_SERVER['HTTP_HOST']}/password?code=" . Crypt::encrypt($user->sid);
            list($ret, $error) = Helper::sendEmail($user->email, 'Reset User Password', 'email.password', [
                'username' => $user->username,
                'webName' => config('sys.web.name', 'app.name'),
                'url' => $url
            ]);
            if (!$ret) {
                $result['message'] = 'Failed to send email: ' . $error;
            } else {
                $result = ['status' => 0, 'message' => "The password reset link has been sent to your email: {$user->email}, please check it."];
            }
        }
        return $result;
    }

    public function verify(Request $request)
    {
        $code = $request->get('code');
        if ($sid = Crypt::decrypt($code)) {
            if ($user = User::where('sid', $sid)->where('status', 1)->first()) {
                $user->status = 2;
                $user->sid = md5(uniqid() . Str::random());
                $user->save();
                
                // 处理邀请奖励
                $this->processInvitationReward($user);
                
                abort(200, 'Activation certification successful@/home');
            }
        }
        abort(500, 'Link expired@/');
    }
    
    /**
     * 处理邀请奖励
     */
    private function processInvitationReward($user)
    {
        // 检查是否开启邀请功能
        if (config('sys.invite.enabled', 0) != 1) {
            return;
        }
        
        // 查找邀请记录
        $invitation = \App\Models\Invitation::where('invitee_uid', $user->uid)
            ->where('status', 0)
            ->first();
            
        if (!$invitation) {
            return;
        }
        
        // 获取邀请人
        $inviter = User::find($invitation->inviter_uid);
        if (!$inviter) {
            return;
        }
        
        // 获取奖励配置
        $inviterPoint = intval(config('sys.invite.inviter_point', 0));
        $inviteePoint = intval(config('sys.invite.invitee_point', 0));
        
        // 给邀请人发放积分
        if ($inviterPoint > 0) {
            User::point($inviter->uid, '邀请奖励', $inviterPoint, '邀请用户 ' . $user->username . ' 注册并验证邮箱获得奖励');
        }
        
        // 给被邀请人发放积分
        if ($inviteePoint > 0) {
            User::point($user->uid, '邀请注册奖励', $inviteePoint, '通过 ' . $inviter->username . ' 的邀请链接注册并验证邮箱获得奖励');
        }
        
        // 更新邀请记录状态
        $invitation->status = 1; // 已验证并奖励
        $invitation->save();
    }

    public function reg(Request $request)
    {
        $result = ['status' => 1];
        $verify = config('sys.user.email', 0);
        $password = $request->post('password');
        $data = [
            'username' => $request->post('username'),
            'password' => Hash::make($password),
            'email' => strtolower($request->post('email')),
            'point' => abs(intval(config('sys.user.point', 0))),
            'sid' => md5(uniqid() . Str::random()),
            'status' => $verify ? 1 : 2
        ];
        if (!config('sys.user.reg', 0)) {
            $result['message'] = 'Sorry, registration is currently closed.';
        } elseif (strtolower($request->post('code')) !== Session::get('captcha_code')) {
            $result['message'] = 'Incorrect verification code.';
        } elseif (!Helper::checkEmail($data['email'])) {
            $result['message'] = 'Invalid email format.';
        } elseif (!Helper::isEmailInWhitelist($data['email'])) {
            $result['message'] = 'This email domain is not on the whitelist. Please check available email domains.';
            $result['whitelist_url'] = url('/email-whitelist');
        } elseif (strlen($data['username']) < 4) {
            $result['message'] = 'Username is too short.';
        } elseif (strlen($password) < 5) {
            $result['message'] = 'Password is too simple.';
        } elseif (User::where('username', $data['username'])->first()) {
            $result['message'] = 'This username has already been registered.';
        } elseif (User::where('email', $data['email'])->first()) {
            $result['message'] = 'This email address has already been registered.';
        } elseif ($user = User::create($data)) {
            // 处理邀请关系
            $inviteCode = $request->post('invite');
            if (!empty($inviteCode) && config('sys.invite.enabled', 0) == 1) {
                $inviter = User::where('uid', $inviteCode)->first();
                if ($inviter) {
                    \App\Models\Invitation::create([
                        'inviter_uid' => $inviter->uid,
                        'invitee_uid' => $user->uid,
                        'status' => 0 // 未验证
                    ]);
                }
            }
            
            if ($data['status'] === 2) {
                $result = ['status' => 0, 'message' => "Congratulations, registration successful! You can login now."];
            } else {
                Helper::sendVerifyEmail($user);
                $result = ['status' => 0, 'message' => "Congratulations, registration successful. An activation email has been sent to your email address: {$data['email']}, please check."];
            }
        } else {
            $result['message'] = 'Registration failed, please try again later.';
        }
        return $result;
    }

    public function check(Request $request)
    {
        $result = ['status' => 1];
        list($name, $error) = Helper::checkDomainName($request->post('name'));
        $did = $request->post('did');
        if (!$name) {
            $result['message'] = $error;
        } elseif (!$domain = Domain::where('did', $did)->first()) {
            $result['message'] = 'Domain does not exist or is not available for use.';
        } else {
            if (DomainRecord::where('did', $did)->where('name', $name)->first()) {
                $result['message'] = 'This domain is already in use.';
            } else {
                $result = ['status' => 0, 'message' => "{$name}.{$domain->domain} is available!"];
            }
        }
        return $result;
    }

    public function captcha(Request $request)
    {
        $phrase = new PhraseBuilder();
        // 设置验证码位数
        $code = $phrase->build(5);
        // 生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder($code, $phrase);
        // 设置背景颜色
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(30);
        $builder->setMaxBehindLines(5);
        $builder->setMaxFrontLines(5);
        // 可以设置图片宽高及字体
        $builder->build($width = 120, $height = 40, $font = null);
        // 获取验证码的内容
        $phrase = $builder->getPhrase();
        // 把内容存入session
        Session::flash('captcha_code', $phrase);
        // 生成图片
        $builder->output();
        $content = ob_get_clean();
        return response($content, 200, ['Content-Type' => 'image/jpeg',]);
    }

    /**
     * 显示允许注册的邮箱白名单列表
     */
    public function emailWhitelist()
    {
        // 获取白名单配置
        $whitelist = config('sys.user.email_whitelist');
        
        // 如果白名单为空，返回提示信息
        if (empty($whitelist)) {
            $content = "# No email whitelist restrictions, any valid email can register\n";
            $content .= "# 当前系统未设置邮箱白名单限制，任何有效邮箱均可注册\n";
            $content .= "# 目前系統未設置郵箱白名單限制，任何有效郵箱均可註冊\n";
            $content .= "# 現在、電子メールのホワイトリスト制限はなく、有効な電子メールは誰でも登録できます\n";
            $content .= "# 현재 이메일 화이트리스트 제한이 없으며 유효한 이메일은 누구나 등록할 수 있습니다\n";
            $content .= "# Actuellement, il n'y a pas de restrictions de liste blanche pour les e-mails, n'importe quel e-mail valide peut s'inscrire\n";
            $content .= "# Nuntempe ne estas retpoŝta blanka listo limigoj, iu ajn valida retpoŝto povas registriĝi\n";
        } else {
            // 将白名单处理为多行文本
            $whitelistDomains = explode("\n", $whitelist);
            $validDomains = [];
            
            foreach ($whitelistDomains as $domain) {
                $domain = trim($domain);
                if (!empty($domain)) {
                    $validDomains[] = $domain;
                }
            }
            
            // 生成说明信息
            $content = "# The following is a list of email domains allowed for registration\n";
            $content .= "# 以下是系统允许注册的邮箱域名列表\n";
            $content .= "# 以下是系統允許註冊的郵箱域名列表\n";
            $content .= "# 以下は、登録が許可されている電子メールドメインのリストです\n";
            $content .= "# 다음은 등록이 허용된 이메일 도메인 목록입니다\n";
            $content .= "# Voici une liste des domaines de messagerie autorisés pour l'inscription\n";
            $content .= "# Jen listo de retpoŝtaj domajnoj permesitaj por registriĝo\n";
            $content .= "\n";
            
            // 添加域名列表
            $content .= implode("\n", $validDomains);
        }
        
        // 返回纯文本响应
        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * 显示注册页面
     */
    public function registerForm()
    {
        // 如果已登录，则重定向到首页
        if (auth()->check()) {
            return redirect('/');
        }
        
        // 如果注册功能已关闭，重定向到登录页面
        if (!config('sys.user.reg', 0)) {
            return redirect('/login')->with('error', 'Sorry, registration is currently closed.');
        }
        
        // 获取邀请码
        $invite = request()->query('invite');
        
        return view('register', compact('invite'));
    }
    
    /**
     * 显示找回密码页面
     */
    public function forgotPasswordForm()
    {
        // 如果已登录，则重定向到首页
        if (auth()->check()) {
            return redirect('/');
        }
        
        return view('password-request');
    }
}