<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Message;

class MessagesController extends Controller
{
    public function index(Request $request)
    {
        $page = request('page', 0);

        $messages = Message::where(['user_id' => Auth::user()->id])->orderBy('id', 'desc');

        if($page > 0)
            $messages->skip($page-1 * 50)->take(50);

        $messages = $messages->get();

        $response = [];

        foreach($messages as $row){

            $response[] = [

                'id'            => $row->id,
                'status'        => strtoupper($row->status),
                'scheduled'     => $row->scheduled,
                'recipients'    => number_format($row->sms_bank->count()),
                'body'          => $row->body,
                'sender'        => $row->sender_name,
                'cost'          => number_format($row->sms_units * 4),
                'date'          => date('M d, Y', strtotime($row->created_at)),

            ];
        }

        return response()->json(['status' => true, 'data' => $response]);
    }

}
