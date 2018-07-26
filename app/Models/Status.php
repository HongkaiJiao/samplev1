<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    // 该属性用于过滤用户提交的字段：只有包含在该属性中的字段才能够被正常更新
    protected $fillable = ['content'];

    // 定义多对一
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
