<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    // 显示注册页面
    public function create()
    {
        return view('users.create');
    }

    // 显示用户个人信息页面
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }

    // 注册功能
    public function store(Request $request)
    {
        // 表单验证规则写法:'标签name值' => '具体验证规则'
        $this->validate($request,[
            'name' => 'required|min:6|max:30',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed'
        ]);
        // 请求过来的数据存入数据库
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        // 欢迎信息入会话闪存
        session()->flash('success','欢迎开启你的laravel5之旅~');
        // 重定向到个人信息页面
        return redirect()->route('users.show',[$user]);
    }
}
