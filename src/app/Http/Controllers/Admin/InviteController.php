<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    /**
     * 显示邀请记录列表
     */
    public function index()
    {
        return view('admin.user.invite');
    }

    /**
     * 处理AJAX请求
     */
    public function post(Request $request)
    {
        $action = $request->input('action');
        
        switch ($action) {
            case 'inviteList':
                return $this->getInviteList($request);
            default:
                return response()->json([
                    'status' => 1,
                    'message' => '未知操作'
                ]);
        }
    }

    /**
     * 获取邀请记录列表
     */
    private function getInviteList(Request $request)
    {
        $query = Invitation::with(['inviter', 'invitee']);
        
        // 状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        
        // 关键词搜索
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $users = User::where('username', 'like', "%{$keyword}%")
                ->orWhere('uid', $keyword)
                ->pluck('uid')
                ->toArray();
                
            $query->where(function($q) use ($users) {
                $q->whereIn('inviter_uid', $users)
                  ->orWhereIn('invitee_uid', $users);
            });
        }
        
        $data = $query->orderBy('id', 'desc')->paginate(15);
        
        return response()->json([
            'status' => 0,
            'message' => 'success',
            'data' => $data
        ]);
    }
}