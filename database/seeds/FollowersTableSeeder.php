<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 获取全部用户
        $users = User::all();
        // 获取第一个用户
        $user = $users->first();
        // 获取第一个用户的id
        $user_id = $user->id;
        // 获取除第一用户外的其他所有用户的id数组
        $followers = $users->slice($user_id);
        $follower_ids = $followers->pluck('id')->toArray();
        // 1号用户关注除自己外的其他所有用户
        $user->follow($follower_ids);
        // 除1号用户外的其他所有用户关注1号用户
        foreach ($followers as $follower) {
            $follower->follow($user_id);
        }
    }
}
