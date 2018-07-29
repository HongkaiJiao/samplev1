<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Mail;
use Auth;


class UsersController extends Controller
{
    public function __construct()
    {
        // 使用Auth中间件过滤--指定除以下动作外其他动作必须经过登录才能访问
        $this->middleware('auth',[
            'except' => ['create','show','store','index','confirmEmail']
        ]);
        // 只允许未登录用户访问登录页面
        $this->middleware('guest',[
            'only' => ['create'],
        ]);
    }

    // 显示用户列表
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }

    // 显示注册页面
    public function create()
    {
        return view('users.create');
    }

    // 显示用户个人信息页面
    public function show(User $user)
    {
        // 分页读取该用户的所有微博
        $statuses = $user->statuses()->orderBy('created_at','desc')->paginate(30);
        return view('users.show',compact('user','statuses'));
    }

    // 注册功能
    public function store(Request $request)
    {
        // 表单验证规则写法:'标签name值' => '具体验证规则'
        $this->validate($request,[
            'name' => 'required|min:6|max:30',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        // 请求过来的数据存入数据库
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        /**
         * (before)
         *
        // 完成注册后自动登录--即将一个已存在的用户实例直接登录到应用
        Auth::login($user);
        // 欢迎信息入会话闪存
        session()->flash('success','欢迎开启你的laravel5之旅~');
        // 重定向到个人信息页面
        return redirect()->route('users.show',[$user]);
         */

        // (after)
        $this->sendEmailConfirmationTo($user);
        session()->flash('success','验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }

    // 显示编辑页面
    public function edit(User $user)
    {
        /**
         * 对 $user 用户进行授权验证;此处参数 update 是指授权类里的 update 授权方法，$user 对应传参 update 授权方法的第二个参数，
         * 默认情况下，无需为 update 授权方法传递第一个参数，即当前登录用户至该方法内，因为框架会自动加载当前登录用户。
         */
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    // 编辑功能
    public function update(User $user,Request $request)
    {
        // nullable 可以为空
        $this->validate($request,[
            'name' => 'required|min:6|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $this->authorize('update',$user);

        // 更新用户对象
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success','个人资料更新成功！');
        return redirect()->route('users.show',$user->id);
    }

    // 删除用户功能
    public function destroy(User $user)
    {
        // 对用户进行授权验证
        if($this->authorize('destroy',$user)){
            $user->delete();
            session()->flash('success','成功删除用户！');
        }

        return back();
    }

    // 发送邮件至指定用户
    protected function sendEmailConfirmationTo($user)
    {
        // 定义包含邮件消息的视图名称
        $view = 'emails.confirm';
        // 定义邮件消息视图的待传参数据数组
        $data = compact('user');
        // 邮件消息的发送方
        //$from = '273912572@qq.com';
        // 发送方名称
        //$name = 'KevinJiao';
        // 邮件消息的接收方
        $to = $user->email;
        // 邮件消息的主题
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        // 使用 Mail 接口的 send 方法进行邮件发送
        Mail::send($view,$data,function($message) use($to,$subject) {
            $message->to($to)->subject($subject);
        });
    }

    // 邮件激活功能
    public function confirmEmail($token)
    {
        // firstOrFail 方法取出第一个满足条件的用户，若查询不到指定用户时将返回一个 404 响应
        $user = User::where('activation_token',$token)->firstOrFail();
        // 更新该用户相应信息
        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        // 完成激活后自动登录--即将一个已存在的用户实例直接登录到应用
        Auth::login($user);
        // 提示信息入会话闪存
        session()->flash('success','恭喜你，激活成功！开启你的laravel5之旅吧~');
        // 重定向到个人信息页面
        return redirect()->route('users.show',[$user]);
    }

    // 显示用户的关注人列表
    public function followings(User $user)
    {
        // 分页获取当前用户的关注人列表
        $users = $user->followings()->paginate(30);
        $title = '关注的人';
        return view('users.show_follow',compact('users','title'));
    }

    // 显示用户的粉丝列表
    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = '粉丝';
        return view('users.show_follow',compact('users','title'));
    }
}
