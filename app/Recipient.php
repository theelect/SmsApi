<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Message;
use App\User;
use App\Contact;
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
		$settings = DB::table('customized_recipients')->where(['id' => $message->customized_recipients_id])->first();


		if($settings == null)  return [];


		return DB::table('contacts')->select('contacts.*')

		->leftJoin('contact_group', 'contacts.id', '=', 'contact_group.contact_id')

		->where(['user_id' => $user->id])

		->where(['status' => 'active'])

		->where(function($query) use ($settings){

			$age_bracket 	= json_decode($settings->age_bracket);
			$gender 		= json_decode($settings->gender);
			$marital_status	= json_decode($settings->marital_status);
			$birth_month	= json_decode($settings->birth_month);
			$state			= json_decode($settings->state);
			$group			= json_decode($settings->group);

			$query->whereIn('contacts.gender', ($gender == null ? ['male', 'female', 'none'] : $gender));

			if($age_bracket != null)
				$query->whereIn('contacts.age_bracket_id', $age_bracket);

			if($marital_status != null)
				$query->whereIn('contacts.marital_status', $marital_status);

			if($birth_month != null)
				$query->whereIn('contacts.month', $birth_month);

			if($state != null)
				$query->whereIn('contacts.state_id', $state);

			if($group != null)
				$query->whereIn('contact_group.group_id', $group);

		})->distinct()->get();
	}
}
