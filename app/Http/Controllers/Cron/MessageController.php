<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Recipient;
use App\Message;
use App\Transaction;
use App\Setting;
use App\Schedule;
use App\User;
use App\SMSVendors;
use DB;
use App\Http\Controllers\Cron\SMSBankController;

class MessageController extends Controller
{

	public function run()
	{		
		$message = Message::where(['status' => 'queued', 'scheduled' => 0])->first();

		if($message != null)
			$this->populateBank($message);

		$schedule = Schedule::where(['status' => 'queued'])->first();

		if($schedule != null){

			$this->populateBank($schedule->message);
			$schedule->update(['status' => 'completed']);
		}

		$this->dispatchSMS();
	}

	private function populateBank(Message $message = null)
	{
		if($message == null)
			return;

		$recipients = Recipient::recipients($message->id);
		$cost 		= count($recipients) * ceil((strlen($message->body) / 160)) * optional($message->user->setting)->units_per_sms;

		if(Transaction::balance($message->user_id) < $cost){

			_log('You do not have enough credit balance to send this message. [Message : '.$message->body.']', $message->user_id);
			Message::where(['id' => $message->id])->update(['status' => 'cancelled']);
			return;
		}

		if($message->recipients_type == 'custom'){

			$this->custom($message, $recipients);

		}else{

			$this->customized($message, $recipients);
		}

		Message::where(['id' => $message->id])->update(['status' => 'completed']);

		Transaction::create([

			'user_id'		=> $message->user_id,
			'quantity'		=> $cost * -1,
			'description'	=> $message->body,
			'message_id'	=> $message->id
		]);
	}

	private function custom(Message $message = null, $recipients = [])
	{
		foreach($recipients as $row){

			DB::table('sms_bank')->insert([

				'message_id'		=> $message->id,
				'phone' 			=> $row->phone,
				'sms'				=> _cleanText($message->body),
				'created_at' 		=> date('Y-m-d H:i:s')

			]);
		}

		return true;
	}

	private function customized(Message $message = null, $recipients = [])
	{
		
		foreach($recipients as $row){

			$body = $message->body;

			if(strripos($message->body, '#name#')){

				$body = str_replace('#name#', ucfirst($row->firstname), $message->body);
			}
			
			DB::table('sms_bank')->insert([

				'message_id'	=> $message->id,
				'phone' 		=> $row->phone,
				'sms'			=> _cleanText($body),
				'created_at' 	=> date('Y-m-d H:i:s')
			]);
		}

	}

	private function dispatchSMS()
	{
		$messages = DB::table('sms_bank')->where(['status' => 'queued'])->limit(50)->get();

		foreach($messages as $row){

			$message = Message::where(['id' => $row->message_id])->first();

			if($message->scheduled == 1 && $message->schedule_time > date('H:i:s'))
				continue;

			$source = strtoupper(substr($message->sender_name, 0, 11));

			$response = SMSVendors::routeSMS($source, $row->sms, $row->phone);

			DB::table('sms_bank')->where(['id' => $row->id])->update([

				'status' 		=> 'sent',
				'response'		=> $response,
				'updated_at'	=> date('Y-m-d H:i:s')
			]);

		}
	}

}
