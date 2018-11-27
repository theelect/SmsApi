<?php

namespace App\Http\Controllers\Messages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Message;
use App\Recipient;

class ReviewController extends Controller
{
	public function index(Request $request, $id = null){

		$message = Message::where(['id'=> $id])->first();

		if($message != null && $message->user->id == _id()){

			$data['message_id']         = $id;
			$data['message']            = $message;
			$data['recipients']         = Recipient::recipients($message->id);

			return view('messages.new.review', $data);
		}
		
		abort(404);
	}
}
