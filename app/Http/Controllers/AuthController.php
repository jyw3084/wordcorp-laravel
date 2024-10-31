<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Mail;
use App\Mail\ResetPassword;

class AuthController extends Controller
{
    public function authLogin(Request $request)
    {
        if (Auth::attempt(['email' => $request['email'], 'password' => $request['password'], 'active' => 1, 'user_type' => 3])) {
            $user = User::where('email', $request['email'])->first();
            Auth::login($user);
            return Auth::User()->roles;
        }
        else{
            return 0;
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
      }


    public function authAjax(Request $request){
        $type = $request['type'];
        switch ($type) {
            case 'forgotPassword':
                $checkUser = User::where('email',$request['reset_email'])->first();
                if($checkUser){
                    $date = date('Y-m-d h:i:s');
                    $date = preg_replace('/[^a-zA-Z0-9_ -]/s','',$date);
                    $date = str_replace('-','',$date);
                    $date = str_replace(' ','',$date);

                    $email = $request['reset_email'];
                    $qouted = $email.'/'.$date;
                    $encrypted_email = bin2hex($qouted);

                    $data = array(
                        'email' => $request['reset_email'],
                        'base_url' => url('/').'/email-password-reset/'.$encrypted_email,
                        'logo' => '',
                        'name'=> 'Admin WordCorp',
                        'subject'=> 'Passwpord Reset Confirmation',
                        'body' => 'Password reset'
                    );
            
                    Mail::to($request['reset_email'])->send(new ResetPassword($data));
                    return '1';
                } else {
                    return '0';
                }

            break;

            case 'changePassword':

                if(Hash::check($request->oldPassword, Auth::user()->password, [])){
                    User::find(Auth::user()->id)->update(
                        [
                            'password'=> bcrypt($request['newPassword'])
                        ]);
                    return 'success';
                } else {
                    return '0';
                }

            break;
            
        }
    }
}
