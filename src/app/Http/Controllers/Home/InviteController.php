<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InviteController extends Controller
{
    /**
     * 显示邀请页面
     */
    public function index()
    {
        return view('home.invite');
    }

    /**
     * 获取邀请列表
     */
    public function getInviteList(Request $request)
    {
        $query = Invitation::with('invitee')
            ->where('inviter_uid', Auth::id())
            ->orderBy('id', 'desc');
            
        $data = $query->paginate(10);
        
        return response()->json([
            'status' => 0,
            'message' => 'success',
            'data' => $data
        ]);
    }
} 