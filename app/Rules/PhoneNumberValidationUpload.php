<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Auth;
use DB;
use Request;

class PhoneNumberValidationUpload implements Rule
{

    public $phone;

    public function passes($attribute, $value)
    {
        $this->phone = config('settings.nigeria_prefix').substr($value, -10);
        
    
        $where = ['user_id' => Auth::user()->id, 'phone' => $this->phone, 'deleted_at' => null];
        return DB::table('contacts')->where($where)->count() < 1 ? true : false;
    }

    public function message()
    {
        $user   = DB::table('contacts')->where(['phone' => $this->phone])->first();

        return 'This phone number is currently been used by '.@$user->firstname.' '.@$user->lastname.'. You cannot have duplicate phone numbers.';
    }
}
