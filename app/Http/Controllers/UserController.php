<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models;

class UserController extends Controller
{
    public function registration(Request $request)
    {
        $user = new User;
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->phone_number = $request['phone_number'];
        $user->password = bcrypt($request['password']);
        $user->user_type = 3;
        $user->roles = 0;

        if($user->save()){
            $user->save();
            return 'success';
        }
        else{
            return 'error';
        }
    }
    public function password_reset(Request $request){
        $user = User::where(['email' => $request['email'], 'active' => 1, 'user_type' => 3])->first();
        $user->password = bcrypt($request['password']);
        if($user->save()){
            $user->save();
            return 1;
        }
        else{
            return 0;
        }
    }
}
