<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable; // 授权相关功能的引用

class User extends Authenticatable
{
    use Notifiable; // 消息通知相关功能引用

    protected $table = 'users'; // 指明要进行数据库交互的数据库表名称

    /**
     * The attributes that are mass assignable.
     * 该属性用于过滤用户提交的字段：只有包含在该属性中的字段才能够被正常更新
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * 该属性用于在用户实例通过数组或 JSON 显示敏感信息时进行隐藏
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // boot 方法会在用户模型类完成初始化之后进行加载，因此我们对事件的监听需要放在该方法中。
    public static function boot()
    {
        parent::boot();
        // creating 方法在模型被创建之前调用
        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }

    // 生成用户头像地址
    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    // 修改用于向用户发送密码重置链接的通知类--自定义用于重置的邮件
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    // 定义一对多关联
    public function statuses()
    {
        // hasMany('App\要引入的实体类名','另一表中的外键','本表中的主键')
        // Eloquent 假定 Status 模型对应到 User 模型上的那个外键字段是 user_id
        return $this->hasMany(Status::class);
    }

    // 从DB中取出当前用户发布过的所有微博
    public function feed()
    {
        //return $this->statuses()->orderBy('created_at','desc');

        $user_ids = Auth::user()->followings->plunk('id')->toArray();
        array_push($user_ids,Auth::user()->id);
        return Status::whereIn('user_id',$user_ids)->with('user')->orderBy('created_at','desc');
    }

    // 获取粉丝关系列表--多对多关联
    public function followers()
    {
        // eg: belongsToMany(User::class,'followers','user_id','follower_id');
        //     belongsToMany(粉丝表,中间表,当前model在中间表中的字段,目标model在中间表中的字段)
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }

    // 获取用户关注人列表
    public function followings()
    {
        return $this->belongsToMany(User::class,'followers','follower_id','user_id');
    }

    // 关注操作
    public function follow($user_ids)
    {
        if (!is_array($user_ids)) { // 参数不为数组时转换为数组
            $user_ids = compact('user_ids');
        }
        // sync 方法在中间表上创建一个多对多记录
        $this->followings()->sync($user_ids,false);
    }

    // 取关操作
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        // detach 方法在中间表上移除一个记录
        $this->followings()->detach($user_ids);
    }

    // 判断当前用户是否关注了目标用户
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
