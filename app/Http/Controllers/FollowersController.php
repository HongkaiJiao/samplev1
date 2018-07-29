<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class FollowersController extends Controller
{
    public function __destruct()
    {
        $this->middleware('auth');
    }

    // 关注功能
    public function store(User $user)
    {
        // 自己关注自己时跳回首页
        if (Auth::user()->id === $user->id) {
            return redirect('/');
        }
        // 未关注则关注
        if (!Auth::user()->isFollowing($user->id)) {
            Auth::user()->follow($user->id);
        }
        // 跳转至关注者的个人信息页面
        return redirect()->route('users.show',$user->id);
    }

    // 取关功能
    public function destroy(User $user)
    {
        // 自己取关自己时跳回首页
        if (Auth::user()->id === $user->id) {
            return redirect('/');
        }
        // 已关注则取关
        if (Auth::user()->isFollowing($user->id)) {
            Auth::user()->unfollow($user->id);
        }
        return redirect()->route('users.show',$user->id);
    }
}
