<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GithubAuth extends Model
{
    protected $table = 'github_auths';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'uid', 'github_id', 'github_login', 'github_name', 'github_email',
        'github_created_at', 'access_token', 'created_at', 'updated_at'
    ];

    /**
     * 检查GitHub账号是否符合要求（注册时间超过半年）
     *
     * @return bool
     */
    public function isQualified()
    {
        $requiredDays = intval(config('sys.github_auth_required_days', 180));
        $githubCreatedAt = strtotime($this->github_created_at);
        $minCreationTime = time() - ($requiredDays * 86400);
        
        return $githubCreatedAt <= $minCreationTime;
    }

    /**
     * 关联用户模型
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
    
    /**
     * 根据用户ID获取GitHub认证信息
     *
     * @param int $uid 用户ID
     * @return GithubAuth|null
     */
    public static function getByUid($uid)
    {
        return self::where('uid', $uid)->first();
    }
    
    /**
     * 根据GitHub ID获取认证信息
     *
     * @param string $githubId GitHub用户ID
     * @return GithubAuth|null
     */
    public static function getByGithubId($githubId)
    {
        return self::where('github_id', $githubId)->first();
    }
} 