<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use \App\Setting;

class SettingsController extends Controller
{
    public function index(Request $request)
    {	
    	
    	return view('settings.index', ['setting' => Setting::firstOrNew(['user_id' => _id()])]);
    }

    public function store(Request $request)
    {
    	$this->validate($request, [

    		'sender_name'                 => 'required|alpha_num|max:15',
    		'birthday_sms_time'           => 'required',
            'birthday_reminder_number'    => 'required|between:11,13'
    	]);

    	$data['user_id'] 					= _id();
    	$data['sender_name']				= request('sender_name');
    	$data['birthday_sms_time']			= request('birthday_sms_time');
    	$data['birthday_reminder_number']	= _tophone(request('birthday_reminder_number'));

        Setting::updateOrCreate(['user_id' => _id()], $data);
        _log('Settings Updated.');

        return redirect('settings')->with('status', 'Your changes have been saved succesfuly.');
    }
}
