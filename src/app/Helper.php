<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2019/4/14
 * Time: 17:37
 */

namespace App;

use App\Models\Domain;
use App\Models\DomainRecord;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class Helper
{
//是否是Pjax请求
    public static function isPjax()
    {
        return request()->header('X-PJAX') === 'true';
    }

    //根据IP获取城市名称
    public static function getIpCity($ip = null)
    {
        $ip = $ip ? $ip : request()->getClientIp();
        $client = static::client();
        $res = $client->get("http://ip.ws.126.net/ipquery?ip={$ip}");
        if ($res->getStatusCode() === 200) {
            $body = (string)$res->getBody();
            $body = mb_convert_encoding($body, 'UTF-8', 'GBK');
            $body = explode('localAddress=', $body);
            if ($ret = json_decode(str_replace(['city', 'province'], ['"city"', '"province"'], $body[1]))) {
                return str_replace('省', '', $ret->province) . str_replace('市', '', $ret->city);
            }
        }
        return 'Unknown';
    }

    /**
     * @return Client
     */
    public static function client()
    {
        return new Client([
            'timeout' => 60,
            'http_errors' => false,
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8'
            ]
        ]);
    }

    //是否是手机访问
    public static function isMobile()
    {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }


    //获取可用域名列表
    public static function getAvailableDomains()
    {
        return Domain::available()->get();
    }

    //获取所有域名列表（不考虑用户组权限）
    public static function getAllDomains()
    {
        return Domain::all();
    }

    //检查域名前缀是否可用
    public static function checkDomainName($name)
    {
        $name = strtolower(trim($name));
        $reserve = explode(',', config('sys.reserve_domain_name'));
        
        // 检查是否是SRV记录特殊格式 (_service._protocol 或 _service._protocol.xxx)
        if (preg_match('/^_[a-z0-9\-]+\._[a-z0-9\-]+/', $name)) {
            // 这是一个有效的SRV记录格式，直接返回有效
            return [$name, null];
        }
        
        // 检查是否为二级域名（包含点号）
        if (strpos($name, '.') !== false) {
            // 分割域名部分
            $parts = explode('.', $name);
            $prefix = array_pop($parts); // 获取最后一个部分，即一级域名
            $subdomain = implode('.', $parts); // 获取前面的部分，即二级域名
            
            // 检查一级域名是否在保留列表中
            if (in_array($prefix, $reserve)) {
                return [false, 'Sorry, this prefix is currently reserved'];
            }
            
            // 检查用户是否有权限在该域名下添加二级域名
            $canUseSubdomain = static::canManageSubdomain($prefix);
            if (!$canUseSubdomain[0]) {
                return [false, $canUseSubdomain[1]];
            }
            
            // 检查二级域名部分格式
            if (!preg_match('/^[a-z0-9\_\-]+$/', $subdomain)) {
                return [false, 'Subdomain format is incorrect'];
            }
            
            return [$name, null];
        }
        
        // 一级域名的原有检查逻辑
        if (strlen($name) < 1) {
            return [false, 'Please enter a domain prefix'];
        } elseif (!preg_match('/^[a-z0-9\_\-]+$/', $name)) {
            return [false, 'Domain prefix format is incorrect'];
        } elseif (in_array($name, $reserve)) {
            return [false, 'Sorry, this prefix is currently reserved'];
        } else {
            return [$name, null];
        }
    }
    
    /**
     * 检查用户是否有权限管理指定前缀下的二级域名
     * 
     * @param string $prefix 一级域名前缀
     * @return array [是否有权限, 错误消息]
     */
    public static function canManageSubdomain($prefix)
    {
        // 如果用户未登录，无权限
        if (!auth()->check()) {
            return [false, 'Please login first'];
        }
        
        $user = auth()->user();
        
        // 检查用户状态是否正常
        if ($user->status != 2) {
            return [false, 'Please complete the certification first'];
        }
        
        // 查找用户是否拥有此前缀的域名
        $userDomains = DomainRecord::where('uid', $user->uid)
            ->where('name', $prefix)
            ->with('domain')
            ->get();
            
        if ($userDomains->isEmpty()) {
            return [false, "You don't have permission to manage subdomains under '{$prefix}'"];
        }
        
        // 检查用户是否有足够的积分
        $subdomain_point = (int)config('sys.subdomain_point', 0);
        if ($subdomain_point > 0 && $user->point < $subdomain_point) {
            return [false, "Insufficient points. You need {$subdomain_point} points to manage subdomains"];
        }
        
        return [true, null];
    }

    //发送邮件
    public static function sendEmail($to, $subject, $view, $array = [])
    {
        if (!config('sys.mail.host') || !config('sys.mail.port') || !config('sys.mail.username') || !config('sys.mail.password')) {
            return [false, "未配置邮箱信息"];
        } else {
            $mailConfig = config('mail');
            $mailConfig['host'] = config('sys.mail.host');
            $mailConfig['port'] = config('sys.mail.port');
            $mailConfig['username'] = config('sys.mail.username');
            $mailConfig['password'] = config('sys.mail.password');
            $mailConfig['encryption'] = config('sys.mail.encryption');
            $mailConfig['from'] = [
                'address' => config('sys.mail.username'),
                'name' => config('sys.web.name', '二级域名分发')
            ];
            config(['mail' => $mailConfig]);
            try {
                Mail::send($view, $array, function ($message) use ($to, $subject) {
                    $message->to($to)->subject($subject);
                });
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $message = $message ? mb_convert_encoding($e->getMessage(), 'UTF-8') : '发送邮件出错！';
                return [false, $message];
            }
            if (count(Mail::failures()) < 1) {
                return [true, null];
            } else {
                return [false, Mail::failures()];
            }
        }
    }


    //检查邮件格式是否正确
    public static function checkEmail($email)
    {
        // 基本邮箱格式检查
        if (!preg_match('/^[a-zA-Z0-9\.\-\_]+\@([a-zA-Z0-9\_\-]+\.)+[a-zA-Z]+$/i', $email)) {
            return false;
        }
        
        // 检查是否允许带点的邮箱注册
        if (config('sys.user.allow_dot_email', 1) == 0) {
            // 如果不允许带点邮箱，检查@前面的部分是否包含点号
            $parts = explode('@', $email);
            $localPart = $parts[0]; // 邮箱@前面的部分
            
            if (strpos($localPart, '.') !== false) {
                return false; // 包含点号，不允许注册
            }
        }
        
        return true;
    }

    //检查邮箱是否在白名单中
    public static function isEmailInWhitelist($email)
    {
        // 获取白名单配置
        $whitelist = config('sys.user.email_whitelist');
        
        // 如果白名单为空，表示不限制
        if (empty($whitelist)) {
            return true;
        }
        
        // 获取邮箱域名部分
        $parts = explode('@', strtolower($email));
        if (count($parts) != 2) {
            return false;
        }
        
        $domain = $parts[1]; // 例如 gmail.com
        
        // 将白名单按行分割
        $whitelistDomains = explode("\n", $whitelist);
        
        // 检查域名是否在白名单中
        foreach ($whitelistDomains as $whitelistDomain) {
            $whitelistDomain = trim($whitelistDomain);
            if (empty($whitelistDomain)) {
                continue;
            }
            
            if ($domain === $whitelistDomain) {
                return true;
            }
        }
        
        return false;
    }

    //发送激活邮件
    public static function sendVerifyEmail(User $user)
    {
        $url = "http://{$_SERVER['HTTP_HOST']}/verify?code=" . Crypt::encrypt($user->sid);
        return static::sendEmail($user->email, 'Verify Your Email Address', 'email.verify', [
            'username' => $user->username,
            'webName' => config('sys.web.name', 'app.name'),
            'url' => $url
        ]);
    }

    //删除解析记录
    public static function deleteRecord(DomainRecord $record)
    {
        try {
            if ($domain = $record->domain) {
                if ($dns = $domain->dnsConfig) {
                    if ($_dns = \App\Klsf\Dns\Helper::getModel($dns->dns)) {
                        $_dns->config($dns->config);
                        list($ret, $error) = $_dns->deleteDomainRecord($record->record_id, $domain->domain_id, $domain->domain);
                        
                        if (!$ret) {
                            \Illuminate\Support\Facades\Log::warning("DNS API returned error when deleting record: {$error}");
                        }
                        
                        return $ret ? true : false;
                    } else {
                        \Illuminate\Support\Facades\Log::error("Failed to get DNS model for record ID: {$record->id}");
                    }
                } else {
                    \Illuminate\Support\Facades\Log::error("No DNS config found for domain ID: {$domain->did}");
                }
            } else {
                \Illuminate\Support\Facades\Log::error("No domain found for record ID: {$record->id}");
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Exception in deleteRecord: " . $e->getMessage());
            throw $e; // 重新抛出异常以便调用者处理
        }
        
        return false;
    }

    //获取首页链接
    public static function getIndexUrls()
    {
        $list = [];
        $str = config('sys.index_urls');
        $rows = explode("
", $str);
        foreach ($rows as $row) {
            $row = explode('|', trim($row));
            if (count($row) == 2) {
                $list[] = $row;
            }
        }
        return $list;
    }
    
    //获取友情链接
    public static function getFriendLinks()
    {
        $list = [];
        
        // 如果友情链接功能被禁用，返回空数组
        if (config('sys.friendlinks.enabled', 1) != 1) {
            return $list;
        }
        
        $str = config('sys.friendlinks.links');
        if (empty($str)) {
            return $list;
        }
        
        $rows = explode("\n", $str);
        foreach ($rows as $row) {
            $row = trim($row);
            if (empty($row)) {
                continue;
            }
            
            $parts = explode('|', $row);
            if (count($parts) >= 2) {
                $link = [
                    'name' => trim($parts[0]),
                    'url' => trim($parts[1]),
                    'description' => count($parts) > 2 ? trim($parts[2]) : ''
                ];
                $list[] = $link;
            }
        }
        
        return $list;
    }
    
    /**
     * 检测 IP 是否合规，使用 Fuck abuser API
     * 
     * @param string $type 记录类型 (A 或 CNAME)
     * @param string $value 记录值 (IP 或域名)
     * @return bool|string 如果合规返回 true，否则返回错误消息
     */
    public static function checkIPValidity($type, $value)
    {
        // 如果不是A或AAAA记录，直接返回true
        if ($type != 'A' && $type != 'AAAA') {
            // 对于MX和SRV记录，可以额外检查域名格式
            if ($type == 'MX' || $type == 'SRV') {
                // 如果是SRV记录，特殊检查其格式
                if ($type == 'SRV') {
                    return self::validateSRVRecord($value);
                }
                
                // 简单验证域名格式
                if (!filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                    return "Invalid hostname format for {$type} record";
                }
                
                // 成功，返回true
                return true;
            }
            
            // 其他类型直接通过
            return true;
        }
        
        // 剩下的代码处理A和AAAA记录的IP验证
        // 如果是 A 记录，直接检测 IP
        if ($type === 'A') {
            return static::checkIPInFuckAbuser($value);
        } 
        // 如果是 CNAME 记录，先解析获取 IP
        elseif ($type === 'CNAME') {
            $ips = static::resolveDomain($value);
            if (empty($ips)) {
                return 'Invalid CNAME: Unable to resolve domain';
            }
            
            // 检测所有解析出的 IP
            foreach ($ips as $ip) {
                $result = static::checkIPInFuckAbuser($ip);
                if ($result !== true) {
                    return $result;
                }
            }
            
            return true;
        }
        
        // 其他记录类型，默认通过
        return true;
    }
    
    /**
     * 使用 Fuck abuser API 检测 IP 是否在黑名单中
     * 
     * @param string $ip 要检测的 IP 地址
     * @return bool|string 如果合规返回 true，否则返回错误消息
     */
    public static function checkIPInFuckAbuser($ip)
    {
        try {
            $client = static::client();
            
            $response = $client->get("https://fuckabuser.lsdt.top/wp-json/fuckabuser/v1/search/byip?query=" . urlencode($ip));
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['status']) && $data['status'] === 200 && !empty($data['data'])) {
                $reason = isset($data['data'][0]['reason']) ? $data['data'][0]['reason'] : 'Prohibited IP';
                return "This IP address has been blocked on fuck-abuser: " . $reason;
            }
            
            return true;
        } catch (\Exception $e) {
            // API 检测失败时，默认放行
            \Illuminate\Support\Facades\Log::error('Fuck abuser API error: ' . $e->getMessage());
            return true;
        }
    }
    
    /**
     * 解析域名获取 IP 地址
     * 
     * @param string $domain 要解析的域名
     * @return array IP 地址列表
     */
    public static function resolveDomain($domain)
    {
        // 移除可能的协议前缀
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        // 移除路径部分，只保留域名
        $domain = preg_replace('/\/.*$/', '', $domain);
        
        try {
            $ips = gethostbynamel($domain);
            return $ips ?: [];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Domain resolution error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 验证SRV记录的值是否合法
     * 
     * @param string $value SRV记录的目标值
     * @return bool|string 如果合规返回true，否则返回错误消息
     */
    public static function validateSRVRecord($value)
    {
        // 移除尾部的点
        $value = rtrim($value, '.');
        
        // 验证是否是有效的主机名
        if (!filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return 'SRV target must be a valid hostname (e.g. sip.example.com)';
        }
        
        return true;
    }
}