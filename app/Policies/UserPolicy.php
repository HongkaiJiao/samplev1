<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // 用户更新时的权限验证:参数一默认为当前登录用户实例，参数二为要进行授权的用户实例
    public function update(User $curuser,User $user)
    {
        return $curuser->id === $user->id;
    }

    // 删除用户时的权限验证
    public function destroy(User $curuser,User $user)
    {
        return $curuser->is_admin && $curuser->id !== $user->id;
    }
}
