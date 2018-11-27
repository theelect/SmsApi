<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsBank extends Model
{
	protected $table  	= 'sms_bank';
    protected $guarded 	= ['updated_at'];
}
