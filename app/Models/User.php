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

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}
