<?php

namespace App\Policies;

use App\Models\Status;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusPolicy
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

    // 删除微博的权限验证--当前用户id与微博作者id进行比较
    public function destroy(User $user,Status $status)
    {
        return $user->id === $status->user_id;
    }
}
