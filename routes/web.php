<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/','StaticPagesController@home')->name('home');
Route::get('/about','StaticPagesController@about')->name('about');
Route::get('/help','StaticPagesController@help')->name('help');

// 注册路由
Route::get('signup','UsersController@create')->name('signup');
// 定义用户资源路由
Route::resource('users','UsersController');

// 登录、退出路由
Route::get('login','SessionsController@create')->name('login');
Route::post('login','SessionsController@store')->name('login');
Route::delete('logout','SessionsController@destroy')->name('logout');

// 注册邮件激活路由
Route::get('signup/confirm/{token}','UsersController@confirmEmail')->name('confirm_email');

// 密码重置功能路由
// 显示重置密码的邮箱发送页面
Route::get('password/reset','Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
// 邮箱发送重设链接
Route::post('password/email','Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
// 显示密码更新页面
Route::get('password/reset/{token}','Auth\ResetPasswordController@showResetForm')->name('password.reset');
// 执行密码更新操作
Route::post('password/reset','Auth\ResetPasswordController@reset')->name('password.update');

// 微博的创建与删除路由
Route::resource('statuses','StatusesController',['only' => ['store','destroy']]);

// 关注人列表、粉丝列表页面
Route::get('/users/{user}/followings','UsersController@followings')->name('users.followings');
Route::get('/users/{user}/followers','UsersController@followers')->name('users.followers');

// 关注、取关功能路由
Route::post('/users/followers/{user}','FollowersController@store')->name('followers.store');
Route::delete('/users/followers/{user}','FollowersController@destroy')->name('followers.destroy');