<?php

namespace App\Http\Controllers\Messages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Message;

class QueueController extends Controller
{
    public function index(Request $request, $id = 0)
	{
		Message::where(['id' => $id])->update(['status' => 'queued']);

		echo 1;
	}
}
