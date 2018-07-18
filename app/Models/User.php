<?php

namespace App\Models;

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
}
