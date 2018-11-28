<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\AgeBracket;
use Illuminate\Support\Facades\Auth;
use Mail;
use App\Setting;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if(request()->isMethod('post')){

            $this->validate($request, [

                'email'     => 'required|email',
                'password'  => 'required'
            ]);

            if(Auth::attempt(['email' => request('email'), 'password' => request('password')], request('remember'))){

                $user = Auth::user();

                if(!$user->verified_email){

                    Auth::logout();
                    $link = url('auth/resend?email='.$user->email);
                    return redirect('/login')->with('status', "You have not confirmed your email with us. <a href='{$link}'>Resend email</a>");
                }

                return redirect('home');

            }else{

                return redirect('/login')->with('status', 'Invalid Email or Password');

            }
        }

        return view('auth.login');
        
    }

    public function register(Request $request)
    {
        if(request()->isMethod('post')){

            $this->validate($request, [

                'name'      => 'required|string|max:255',
                'email'     => 'required|string|email|max:255|unique:users',
                'phone'     => 'required|string|digits:11|max:11|unique:users',
                'address'   =>'required',
                'password'  => 'required|string|min:6|confirmed'
            ]);


            $user = User::create([

                'name'              => request('name'),
                'email'             => request('email'),
                'phone'             => config('settings.nigeria_prefix').substr(request('phone'), -10),
                'address'           => request('address'),
                'password'          => bcrypt(request('password')),
                'api_token'         => str_random(16)
            ]);

            AgeBracket::insert([

                ['user_id' => $user->id, 'name' => 'teenage'],
                ['user_id' => $user->id, 'name' => 'youth'],
                ['user_id' => $user->id, 'name' => 'adult'],
                ['user_id' => $user->id, 'name' => 'aged']
                
            ]);

            Setting::create([

                'user_id'           => $user->id,
                'member_address'    => 'mr_mrs_miss',
                'sender_name'       => $user->name,
                'units_per_sms'     => 4
            ]);
            
            $subject = 'Verify your account on SMSVent';

            $body = view('emails.verify')->with(['user' => $user])->render();

            _sendEmail($user->email, $subject, $body);

            return redirect('login')->with('status', 'You have succesfully registered on our platform. We have also sent an activation email.  Please login to your email to activate your account.');
        }

        return view('auth.register');
    }

    public function verify($api_token = '')
    {
        $user = User::where('api_token', $api_token)->first();

        if(!$user)
            redirect('login');

        $user->update(['verified_email' => true]);

        Auth::login($user);
        
        return redirect('home')->with('status', 'Email verified succesfully.');
    }

    public function resend(Request $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();

        if($user->verified_email == true){

            return redirect('home')->with('status', 'Your email has already been verified');
        }

        $subject = 'Verify your account on SMSVent';

        $body = view('emails.verify')->with(['user' => $user])->render();

        _sendEmail($user->email, $subject, $body);

        return redirect('login')->with('status', 'Verification email resent. Please check your mail');

    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

    public function impersonate($user_id = 0)
    {
        Auth::loginUsingId($user_id, true);

        return redirect('home');
    }
}
