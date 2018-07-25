<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        // 只允许未登录用户访问登录页面
        $this->middleware('guest',[
            'only' => ['create'],
        ]);
    }

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
        if (Auth::attempt($credentials,$request->has('remember'))) {
            // 判断当前已登录用户的activated字段--账号是否已激活
            if (Auth::user()->activated) {
                session()->flash('success','欢迎回来');
                // Auth 门面的 user 方法用于获取当前认证用户
                // redirect() 实例的 intended 方法可将页面重定向到上一次请求尝试访问的页面上，并接收一个默认跳转地址参数
                return redirect()->intended(route('users.show',[Auth::user()]));
            } else {
                Auth::logout();
                session()->flash('warning','你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }
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
