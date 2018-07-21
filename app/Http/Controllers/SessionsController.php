<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    // 显示登录页面
    public function create()
    {
        return view('sessions.create');
    }

    // 登录功能
    public function store(Request $request)
    {
        // 登录表单验证
        $credentials = $this->validate($request,[
            'email' =>'required|email|max:255',
            'password' =>'required'
        ]);
        // Auth 门面的 attempt 方法用于对用户身份进行认证
        if (Auth::attempt($credentials)) {
            session()->flash('success','欢迎回来');
            // Auth 门面的 user 方法用于获取当前认证用户
            return redirect()->route('users.show',[Auth::user()]);
        } else {
            session()->flash('danger','很抱歉，您的邮箱和密码不匹配');
            // 重定向至上一页面时回带email信息
            return redirect()->back()->withInput(['email'=>$request->email]);
        }
    }

    // 登出功能
    public function destroy()
    {
        // Auth 门面的 logout 方法会清除用户 Session 中的认证信息
        Auth::logout();
        session()->flash('success','您已成功退出！');
        return redirect('login');
    }
}
