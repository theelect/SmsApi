<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Auth;
use DB;
use Request;

class PhoneNumberValidation implements Rule
{

    public $phone;

    public function __construct($phone = null)
    {
        if($phone != null){

            $this->phone = $phone;
        }
    }

    public function passes($attribute, $value)
    {
        $phone = config('settings.nigeria_prefix').substr($value, -10);
        
        if($this->phone != NULL){

            if($this->phone == $phone)
                return true;
        }

        $where = ['user_id' => Auth::user()->id, 'phone' => $phone, 'deleted_at' => null];
        return DB::table('contacts')->where($where)->count() < 1 ? true : false;
    }

    public function message()
    {
        $phone  = config('settings.nigeria_prefix').substr(Request::input('phone'), -10);
        $user   = DB::table('contacts')->where(['phone' => $phone])->first();

        return 'This phone number is currently been used by '.@$user->firstname.' '.@$user->lastname.'. You cannot have duplicate phone numbers.';
    }
}
