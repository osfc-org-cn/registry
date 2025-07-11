<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2019/4/14
 * Time: 16:42
 */

namespace App\Http\Controllers\Admin;


use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserPointRecord;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function post(Request $request)
    {
        $action = $request->post('action');
        switch ($action) {
            case 'pointRecord':
                return $this->pointRecord($request);
            case 'point':
                return $this->point($request);
            case 'update':
                return $this->update($request);
            case 'select':
                return $this->select($request);
            case 'delete':
                return $this->delete($request);
            case 'inviteList':
                return $this->inviteList($request);
            default:
                return ['status' => -1, 'message' => '对不起，此操作不存在！'];
        }
    }

    private function pointRecord(Request $request)
    {
        $data = UserPointRecord::search('admin')->orderBy('id', 'desc')->pageSelect();
        return ['status' => 0, 'message' => '', 'data' => $data];
    }

    private function point(Request $request)
    {
        $result = ['status' => -1];
        $uid = intval($request->post('uid'));
        $point = intval($request->post('point'));
        $remark = $request->post('remark');
        $act = $request->post('act') ? 1 : 0;
        if (!$uid || !$row = User::find($uid)) {
            $result['message'] = '用户不存在';
        } elseif ($point < 1) {
            $result['message'] = '请输入正确积分数';
        } elseif (User::point($uid, $act ? '扣除' : '增加', $act ? 0 - $point : $point, $remark)) {
            $result = ['status' => 0, 'message' => $act ? '扣除成功' : '增加成功'];
        } else {
            $result['message'] = '操作失败，请稍后再试！';
        }
        return $result;
    }

    private function update(Request $request)
    {
        $result = ['status' => -1];
        $uid = intval($request->post('uid'));
        $data = [
            'gid' => intval($request->post('gid')),
            'status' => intval($request->post('status')),
            'email' => $request->post('email')
        ];
        $password = $request->post('password');
        if (!$uid || !$row = User::find($uid)) {
            $result['message'] = '用户不存在';
        } elseif (!UserGroup::find($data['gid'])) {
            $result['message'] = '用户组不存在';
        } elseif ($password && strlen($password) < 5) {
            $result['message'] = '新密码太简单';
        } else {
            // 检查用户状态是否从非0变为0（被封禁）
            $userWasBanned = ($row->status != 0 && $data['status'] == 0);
            
            if ($password) {
                $data['password'] = Hash::make($password);
                $data['sid'] = md5(uniqid() . Str::random(15));
            }
            if ($row->update($data)) {
                // 如果用户被封禁且有邮箱，发送邮件通知
                if ($userWasBanned && $row->email) {
                    \App\Helper::sendEmail(
                        $row->email,
                        'Account Status Notice',
                        'email.user-banned',
                        [
                            'username' => $row->username,
                            'webName' => config('sys.web.name', 'OSFC Registry')
                        ]
                    );
                }
                
                $result = ['status' => 0, 'message' => '修改成功'];
            } else {
                $result['message'] = '修改失败或未做任何修改！';
            }
        }
        return $result;
    }

    private function select(Request $request)
    {
        $data = User::search()->where('gid', '!=', 99)->orderBy('uid', 'desc')->pageSelect();
        return ['status' => 0, 'message' => '', 'data' => $data];
    }

    private function delete(Request $request)
    {
        $result = ['status' => -1];
        $id = intval($request->post('id'));
        if (!$id || !$row = User::find($id)) {
            $result['message'] = '用户不存在';
        } else {
            // 保存用户邮箱和用户名，用于后续发送邮件
            $userEmail = $row->email;
            $username = $row->username;
            
            if ($row->delete()) {
                // 如果用户有邮箱，发送账户删除通知邮件
                if ($userEmail) {
                    \App\Helper::sendEmail(
                        $userEmail,
                        'Account Deletion Notice',
                        'email.user-deleted', // 使用专门的账户删除邮件模板
                        [
                            'username' => $username,
                            'webName' => config('sys.web.name', 'OSFC Registry')
                        ]
                    );
                }
                
                $result = ['status' => 0, 'message' => '删除成功'];
            } else {
                $result['message'] = '删除失败，请稍后再试！';
            }
        }
        return $result;
    }

    /**
     * 获取邀请记录列表
     */
    private function inviteList(Request $request)
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