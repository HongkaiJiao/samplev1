<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Auth;

class StatusesController extends Controller
{
    // 使用auth中间件--该控制器所有操作必须经过登录才可访问
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 微博创建功能
    public function store(Request $request)
    {
        $this->validate($request,[
            'content' => 'required|max:140'
        ]);
        // 为当前用户实例创建一条微博--即为微博内容赋值
        Auth::user()->statuses()->create([
            'content' => $request->content
        ]);
        return redirect()->back();
    }

    // 微博删除功能
    public function destroy(Status $status)
    {
        // 删除微博的授权判定，不通过则抛403异常
        $this->authorize('destroy',$status);
        // 执行删除操作
        $status->delete();
        session()->flash('success','微博删除成功！');
        return redirect()->back();
    }
}
