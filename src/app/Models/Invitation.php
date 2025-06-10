<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $table = 'invitations';
    protected $primaryKey = 'id';
    protected $fillable = ['inviter_uid', 'invitee_uid', 'status'];

    /**
     * 获取邀请人信息
     */
    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_uid', 'uid');
    }

    /**
     * 获取被邀请人信息
     */
    public function invitee()
    {
        return $this->belongsTo(User::class, 'invitee_uid', 'uid');
    }
} 