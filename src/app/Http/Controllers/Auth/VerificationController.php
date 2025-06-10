<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use App\Models\UserPointRecord;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    // ... 原来的代码 ...
    
    /**
     * 邮箱验证成功后的处理
     */
    protected function verified(Request $request)
    {
        // 更新用户状态
        $user = Auth::user();
        $user->status = 2; // 已验证
        $user->save();
        
        // 处理邀请奖励
        $this->processInvitationReward($user);
    }
    
    /**
     * 处理邀请奖励
     */
    protected function processInvitationReward($user)
    {
        // 检查是否开启邀请功能
        if (config('sys.invite.enabled', 0) != 1) {
            return;
        }
        
        // 查找邀请记录
        $invitation = Invitation::where('invitee_uid', $user->uid)
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
            $inviter->point += $inviterPoint;
            $inviter->save();
            
            // 记录积分变动
            UserPointRecord::create([
                'uid' => $inviter->uid,
                'action' => '邀请奖励',
                'point' => $inviterPoint,
                'rest' => $inviter->point,
                'remark' => '邀请用户 ' . $user->username . ' 注册并验证邮箱获得奖励'
            ]);
        }
        
        // 给被邀请人发放积分
        if ($inviteePoint > 0) {
            $user->point += $inviteePoint;
            $user->save();
            
            // 记录积分变动
            UserPointRecord::create([
                'uid' => $user->uid,
                'action' => '邀请注册奖励',
                'point' => $inviteePoint,
                'rest' => $user->point,
                'remark' => '通过 ' . $inviter->username . ' 的邀请链接注册并验证邮箱获得奖励'
            ]);
        }
        
        // 更新邀请记录状态
        $invitation->status = 1; // 已验证并奖励
        $invitation->save();
    }
} 