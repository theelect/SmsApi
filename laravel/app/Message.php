<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use App\User;

class Message extends Model
{
	use SoftDeletes;

	protected $guarded 	= ['deleted_at'];
	protected $dates 	= ['deleted_at'];

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function sms_bank()
	{
		return $this->hasMany('App\SmsBank');
	}

}
