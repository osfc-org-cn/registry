<?php

namespace App\Http\Controllers;

use App\Models\GithubAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class GithubAuthController extends Controller
{
    /**
     * 重定向到GitHub授权页
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(Request $request)
    {
        if (config('sys.github_auth_enabled', '0') !== '1') {
            return redirect('/')->with('error', 'GitHub authentication is disabled');
        }

        $clientId = config('sys.github_client_id');
        if (empty($clientId)) {
            return redirect('/')->with('error', 'GitHub authentication configuration error');
        }

        $redirectUrl = url('/github/callback');
        $state = md5(uniqid(rand(), true));
        
        // 保存状态和操作类型（绑定或登录）到Session
        $action = $request->query('action', 'bind'); // 默认为绑定操作
        session(['github_state' => $state]);
        session(['github_action' => $action]);
        
        // 如果是登录操作，保存原始请求的URL用于登录后重定向
        if ($action === 'login') {
            session(['github_redirect_after_login' => $request->query('redirect', '/')]);
        }

        $url = 'https://github.com/login/oauth/authorize?' . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUrl,
            'scope' => 'user',
            'state' => $state,
        ]);

        return redirect($url);
    }

    /**
     * 处理GitHub回调
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        if (config('sys.github_auth_enabled', '0') !== '1') {
            return redirect('/')->with('error', 'GitHub authentication is disabled');
        }

        $state = $request->input('state');
        $sessionState = session('github_state');
        if (!$state || $state !== $sessionState) {
            return redirect('/')->with('error', 'Invalid request');
        }

        $code = $request->input('code');
        if (!$code) {
            return redirect('/')->with('error', 'Authorization failed');
        }

        try {
            // 使用Guzzle HTTP客户端
            $client = new Client();
            
            // 获取访问令牌
            $tokenResponse = $client->post('https://github.com/login/oauth/access_token', [
                'headers' => ['Accept' => 'application/json'],
                'form_params' => [
                    'client_id' => config('sys.github_client_id'),
                    'client_secret' => config('sys.github_client_secret'),
                    'code' => $code,
                    'redirect_uri' => url('/github/callback'),
                ]
            ]);
            
            $response = json_decode($tokenResponse->getBody(), true);

            if (!isset($response['access_token'])) {
                return redirect('/')->with('error', 'Failed to get access token');
            }

            $accessToken = $response['access_token'];

            // 获取GitHub用户信息
            $userResponse = $client->get('https://api.github.com/user', [
                'headers' => [
                    'Authorization' => 'token ' . $accessToken,
                    'Accept' => 'application/json',
                ]
            ]);
            
            $userInfo = json_decode($userResponse->getBody(), true);

            // 获取操作类型（绑定或登录）
            $action = session('github_action', 'bind');
            
            // 检查GitHub账号是否已绑定到用户
            $existingAuth = GithubAuth::getByGithubId($userInfo['id']);
            
            // 检查账号注册时间
            $requiredDays = intval(config('sys.github_auth_required_days', 180));
            $githubCreatedAt = strtotime($userInfo['created_at']);
            $minCreationTime = time() - ($requiredDays * 86400);
            
            // 如果是登录操作
            if ($action === 'login') {
                // 如果GitHub账号已绑定到某个用户
                if ($existingAuth) {
                    $user = User::find($existingAuth->uid);
                    if ($user && $user->status != 0) { // 确保用户账号未被禁用
                        // 登录用户
                        Auth::login($user);
                        
                        // 更新访问令牌
                        $existingAuth->update([
                            'access_token' => $accessToken,
                            'updated_at' => time(),
                        ]);
                        
                        // 重定向到登录后的页面
                        $redirectUrl = session('github_redirect_after_login', '/');
                        session()->forget(['github_state', 'github_action', 'github_redirect_after_login']);
                        return redirect($redirectUrl)->with('success', 'Logged in successfully with GitHub');
                    } else {
                        return redirect('/login')->with('error', 'This account is disabled or does not exist');
                    }
                } else {
                    // GitHub账号未绑定到任何用户，提示注册并绑定
                    session(['github_user_info' => $userInfo]);
                    session(['github_access_token' => $accessToken]);
                    return redirect('/register')->with('notice', 'Your GitHub account is not linked to any user. Please register or login first, then link your account.');
                }
            }
            // 如果是绑定操作
            else {
                if (Auth::check()) {
                    $user = Auth::user();
                    
                    // 检查GitHub账号是否已绑定到其他用户
                    if ($existingAuth && $existingAuth->uid != $user->uid) {
                        return redirect('/home/profile')->with('error', 'This GitHub account is already linked to another user');
                    }
                    
                    // 更新或创建GitHub认证信息
                    GithubAuth::updateOrCreate(
                        ['uid' => $user->uid],
                        [
                            'github_id' => $userInfo['id'],
                            'github_login' => $userInfo['login'],
                            'github_name' => $userInfo['name'] ?? null,
                            'github_email' => $userInfo['email'] ?? null,
                            'github_created_at' => $userInfo['created_at'],
                            'access_token' => $accessToken,
                            'created_at' => time(),
                            'updated_at' => time(),
                        ]
                    );

                    $message = $githubCreatedAt <= $minCreationTime ? 
                        'GitHub account verified successfully. You can now add NS and MX records.' : 
                        'GitHub account linked successfully, but your account is less than ' . $requiredDays . ' days old. You cannot add NS and MX records yet.';

                    return redirect('/home/profile')->with('success', $message);
                } else {
                    return redirect()->route('login')->with('error', 'Please login first');
                }
            }
        } catch (\Exception $e) {
            Log::error('GitHub OAuth authentication failed: ' . $e->getMessage());
            return redirect('/')->with('error', 'GitHub authentication failed: ' . $e->getMessage());
        }
    }
    
    /**
     * 解绑GitHub账号
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unbind()
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 1,
                'message' => 'Please login first'
            ]);
        }
        
        $user = Auth::user();
        $auth = GithubAuth::getByUid($user->uid);
        
        if ($auth) {
            $auth->delete();
            return response()->json([
                'status' => 0,
                'message' => 'GitHub account disconnected successfully'
            ]);
        }
        
        return response()->json([
            'status' => 1,
            'message' => 'No GitHub account connected'
        ]);
    }
    
    /**
     * 检查用户是否可以添加NS/MX记录
     *
     * @param int $uid 用户ID
     * @return bool
     */
    public static function canAddSpecialRecords($uid)
    {
        // 如果GitHub认证未启用，允许添加
        if (config('sys.github_auth_enabled', '0') !== '1') {
            return true;
        }
        
        // 获取用户的GitHub认证信息
        $auth = GithubAuth::getByUid($uid);
        if (!$auth) {
            return false;
        }
        
        // 检查GitHub账号是否符合要求
        return $auth->isQualified();
    }
    
    /**
     * GitHub登录入口
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // 添加action=login参数并重定向到GitHub授权页面
        $redirectUrl = url()->current();
        if ($request->has('redirect')) {
            $redirectUrl .= '?redirect=' . $request->query('redirect');
        }
        
        return redirect('/github/redirect?action=login&redirect=' . urlencode($request->query('redirect', '/')));
    }
} 