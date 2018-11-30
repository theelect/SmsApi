<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Message;
use App\User;
use App\Contact;
use App\CustomizedRecipient;
use DB;

class Recipient extends Model
{
    public static function recipients($id = 0)
	{
		$message 	= Message::where(['id' => $id])->first();
		$user 		= User::find($message->user_id);

		if($message == null || $user == null)
			return [];

		if($message->recipients_type == 'custom'){

			$contacts = [];

			foreach(json_decode($message->custom_recipients) as $row){

				$contact 			= new Contact();
				$contact->phone 	= _tophone($row);
				$contacts[] 		= $contact;
			}

			return $contacts;
		}

		if($message->recipients_type == 'all'){

			return Contact::where(['user_id' => $user->id, 'status' => 'active'])->get();
		}

		if($message->recipients_type == 'customize'){

			return Recipient::messageRecipientsCustomize($user, $message);
		}


		return [];
	}

	private static function messageRecipientsCustomize($user = null, $message = null) 
	{ 
		$settings = CustomizedRecipient::where(['id' => $message->customized_recipients_id])->first();

		if($settings == null)  return [];

		return DB::table('contacts')->select('contacts.*')

		->where(['user_id' => $user->id])

		->where(['status' => 'active'])

		->where(function($query) use ($settings){

			$age_bracket 	= json_decode($settings->age_bracket);
			$gender 		= json_decode($settings->gender);
			$birth_month	= json_decode($settings->birth_month);
			$occupation		= json_decode($settings->occupation);

			$query->whereIn('contacts.gender', ($gender == null ? ['male', 'female', 'none'] : $gender));

			// if($age_bracket != null)
			// 	$query->whereIn('contacts.age_bracket_id', $age_bracket);

			if($birth_month != null)
				$query->whereIn('contacts.month', $birth_month);

			if($occupation != null)
				$query->whereIn('contacts.occupation_id', $occupation);

		})->distinct()->get();
	}
}
