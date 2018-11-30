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

	public function scopeBy($query, $user_id)
	{
		return $query->where(['user_id' => $user_id]);
	}

	public static function transform($messages = [])
	{
		$response = [];

		foreach($messages as $row){

			$response[] = [

				'id'            => $row->id,
				'status'        => ($row->status == 'pending') ? 'Scheduled' : 'Sent',
				'scheduled'     => $row->scheduled,
				'recipients'    => number_format($row->sms_bank->count()),
				'body'          => $row->body,
				'sender'        => $row->sender_name,
				'cost'          => number_format($row->sms_units * 4),
				'date'          => _date($row->created_at, true),

			];
		}

		return $response;
	}

}
