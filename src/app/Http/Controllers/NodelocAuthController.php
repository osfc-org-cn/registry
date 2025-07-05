<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserThird;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class NodelocAuthController extends Controller
{
    /**
     * 重定向到Nodeloc授权页面
     */
    public function redirect(Request $request)
    {
        if (config('sys.nodeloc_auth_enabled', '0') !== '1') {
            return redirect('/')->with('error', 'Nodeloc authentication is disabled');
        }

        $clientId = config('sys.nodeloc_client_id');
        if (empty($clientId)) {
            return redirect('/')->with('error', 'Nodeloc authentication configuration error');
        }

        // 生成随机状态
        $state = Str::random(40);
        
        // 将state存储在session中，并确保立即保存
        $request->session()->put('nodeloc_state', $state);
        $request->session()->save();
        
        // 同时在cookie中也存储一份state，以防session丢失
        $cookie = cookie('nodeloc_state', $state, 5); // 5分钟过期
        
        // 增强调试信息
        Log::info('Nodeloc redirect - Generated state', [
            'state' => $state, 
            'session_id' => $request->session()->getId(),
            'session_data' => [
                'nodeloc_state' => $request->session()->get('nodeloc_state'),
                'nodeloc_action' => $request->session()->get('nodeloc_action')
            ],
            'request_method' => $request->method()
        ]);
        
        // 验证会话状态是否正确保存
        if ($request->session()->get('nodeloc_state') !== $state) {
            Log::error('Nodeloc redirect - Session state not saved correctly', [
                'expected' => $state,
                'actual' => $request->session()->get('nodeloc_state')
            ]);
        }

        // 构建授权URL
        $query = http_build_query([
            'client_id' => config('sys.nodeloc_client_id'),
            'redirect_uri' => route('nodeloc.callback'),
            'response_type' => 'code',
            'scope' => 'openid profile',
            'state' => $state,
        ]);

        // 重定向到授权页面
        return redirect('https://conn.nodeloc.cc/oauth2/auth?' . $query)->withCookie($cookie);
    }

    /**
     * 处理Nodeloc授权回调
     */
    public function callback(Request $request)
    {
        if (config('sys.nodeloc_auth_enabled', '0') !== '1') {
            return redirect('/')->with('error', 'Nodeloc authentication is disabled');
        }

        // 获取请求中的state
        $state = $request->input('state');
        
        // 尝试从session中获取state
        $sessionState = $request->session()->get('nodeloc_state');
        
        // 如果session中没有，尝试从cookie中获取
        if (empty($sessionState) && $request->hasCookie('nodeloc_state')) {
            $sessionState = $request->cookie('nodeloc_state');
            Log::info('Nodeloc callback - Using state from cookie instead of session');
        }
        
        // 调试信息 - 打印所有请求参数和session状态
        $debugInfo = [
            'request_all' => $request->all(),
            'request_state' => $state,
            'session_state' => $sessionState,
            'session_id' => $request->session()->getId(),
            'cookie_state' => $request->cookie('nodeloc_state'),
            'session_all' => $request->session()->all()
        ];
        
        Log::info('Nodeloc callback - Debug info', $debugInfo);
        
        // 如果state无效，记录错误并返回调试信息
        if (!$state || $state !== $sessionState) {
            Log::error('Nodeloc callback - Invalid state parameter', $debugInfo);
            
            // 清除会话和cookie中的状态
            $request->session()->forget('nodeloc_state');
            $cookie = cookie()->forget('nodeloc_state');
            
            // 返回到登录页面，附带错误信息
            return redirect('/login')->with('error', 'Authentication failed: Invalid state parameter. Please try again.')->withCookie($cookie);
        }

        // 清除会话和cookie中的状态
        $request->session()->forget('nodeloc_state');
        $cookie = cookie()->forget('nodeloc_state');
        
        // 检查是否有错误或拒绝授权
        if ($request->has('error')) {
            return redirect('/login')->with('error', 'Authorization failed: ' . $request->error_description)->withCookie($cookie);
        }

        // 获取授权码
        $code = $request->input('code');
        if (empty($code)) {
            return redirect('/login')->with('error', 'No authorization code provided')->withCookie($cookie);
        }

        try {
            // 交换授权码获取访问令牌
            $clientId = config('sys.nodeloc_client_id');
            $clientSecret = config('sys.nodeloc_client_secret');
            
            // 使用Guzzle HTTP客户端
            $client = new Client();
            
            // 获取访问令牌
            $tokenResponse = $client->post('https://conn.nodeloc.cc/oauth2/token', [
                'auth' => [$clientId, $clientSecret],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => route('nodeloc.callback'),
                ]
            ]);
            
            $tokens = json_decode($tokenResponse->getBody(), true);
            
            if (!isset($tokens['access_token'])) {
                Log::error('Nodeloc token exchange failed', [
                    'response' => $tokens
                ]);
                return redirect('/login')->with('error', 'Failed to exchange authorization code for access token')->withCookie($cookie);
            }
            
            // 获取用户信息
            $userResponse = $client->get('https://conn.nodeloc.cc/oauth2/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $tokens['access_token'],
                    'Accept' => 'application/json',
                ]
            ]);
            
            $userInfo = json_decode($userResponse->getBody(), true);
            
            // 获取操作类型（绑定或登录）
            $action = $request->session()->get('nodeloc_action', 'login');
            
            // 查找或创建用户
            $userThird = UserThird::where('platform', 'nodeloc')
                ->where('openid', $userInfo['sub'])
                ->first();
                
            if ($userThird) {
                // 用户已存在，直接登录
                $user = User::find($userThird->user_id);
                if (!$user) {
                    return redirect('/login')->with('error', 'User account not found')->withCookie($cookie);
                }
                
                // 更新访问令牌
                $userThird->update([
                    'access_token' => $tokens['access_token'],
                    'refresh_token' => $tokens['refresh_token'] ?? null,
                    'expires_at' => isset($tokens['expires_in']) ? time() + $tokens['expires_in'] : null
                ]);
                
                Auth::guard('web')->login($user);
                return redirect($request->session()->get('url.intended', '/home'))->withCookie($cookie);
            } else {
                // 检查是否已登录
                if (Auth::guard('web')->check()) {
                    // 已登录，绑定账号
                    $user = Auth::guard('web')->user();
                    
                    // 创建第三方账号关联
                    $userThird = new UserThird();
                    $userThird->user_id = $user->uid;
                    $userThird->platform = 'nodeloc';
                    $userThird->openid = $userInfo['sub'];
                    $userThird->nickname = $userInfo['username'] ?? '';
                    $userThird->avatar = $userInfo['picture'] ?? '';
                    $userThird->access_token = $tokens['access_token'];
                    $userThird->refresh_token = $tokens['refresh_token'] ?? null;
                    $userThird->expires_at = isset($tokens['expires_in']) ? time() + $tokens['expires_in'] : null;
                    $userThird->save();
                    
                    return redirect('/home/profile')->with('success', 'Nodeloc account linked successfully')->withCookie($cookie);
                } else {
                    // 未登录，存储Nodeloc用户信息到会话，以便后续注册时使用
                    $request->session()->put([
                        'nodeloc_user_info' => $userInfo,
                        'nodeloc_access_token' => $tokens['access_token'],
                        'nodeloc_refresh_token' => $tokens['refresh_token'] ?? null,
                        'nodeloc_expires_in' => $tokens['expires_in'] ?? null
                    ]);
                    $request->session()->save();
                    
                    // 重定向到注册页面，参考GitHub的实现
                    return redirect('/register')->with('notice', 'Your Nodeloc account is not linked to any user. Please register or login first, then link your account.')->withCookie($cookie);
                }
            }
        } catch (\Exception $e) {
            Log::error('Nodeloc authentication error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->with('error', 'Authentication failed: ' . $e->getMessage())->withCookie($cookie);
        }
    }

    /**
     * Nodeloc登录入口
     */
    public function login(Request $request)
    {
        // 设置action为login，表示这是登录操作
        $request->session()->put('nodeloc_action', 'login');
        
        // 强制保存会话
        $request->session()->save();
        
        // 记录日志
        Log::info('Nodeloc login - Starting login process', [
            'session_id' => $request->session()->getId(),
            'session_data' => $request->session()->all()
        ]);
        
        // 重定向到授权页面
        return $this->redirect($request);
    }
    
    /**
     * 解绑Nodeloc账号
     */
    public function unbind(Request $request)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['status' => 1, 'message' => 'User not logged in']);
        }
        
        $user = Auth::guard('web')->user();
        $userThird = UserThird::where('user_id', $user->uid)
            ->where('platform', 'nodeloc')
            ->first();
            
        if ($userThird) {
            $userThird->delete();
            return response()->json(['status' => 0, 'message' => 'Nodeloc account unlinked successfully']);
        }
        
        return response()->json(['status' => 1, 'message' => 'No Nodeloc account linked']);
    }
} 