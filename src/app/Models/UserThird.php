<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserThird extends Model
{
    protected $table = 'user_third';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    public $timestamps = true; // 启用自动时间戳
    
    /**
     * 关联用户模型
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uid');
    }
} 