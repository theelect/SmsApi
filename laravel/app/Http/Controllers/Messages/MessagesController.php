<?php

namespace App\Http\Controllers\Messages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Message;
use App\Contact;

class MessagesController extends Controller
{
	public function index()
	{
		$data['messages'] = Message::where(['user_id' => _id()])->orderBy('created_at', 'DESC')->get();

		return view('messages.index', $data);
	}

	public function single($id = 0)
	{
		$data['message'] = Message::find($id);

		return view('messages.single', $data);
	}
}
