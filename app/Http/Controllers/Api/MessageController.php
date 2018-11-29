<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Message;
use App\Http\Requests\CreateMessageRequest;

class MessageController extends Controller
{
    public function index(Request $request, $id = 0)
    {
        $id = request('id', $id);

        if(!$id) return response()->json(['status' => false, 'data' => 'Message ID is required'], 400);

        $message = Message::where(['user_id' => Auth::user()->id, 'id' => $id])->first();

        if(!$message) return response()->json(['status' => false, 'data' => 'Message Not Found'], 404);

        $response = [

            'id'            => $message->id,
            'status'        => strtoupper($message->status),
            'scheduled'     => $message->scheduled,
            'recipients'    => number_format($message->sms_bank->count()),
            'body'          => $message->body,
            'sender'        => $message->sender_name,
            'cost'          => number_format($message->sms_units * 4),
            'date'          => _date($message->created_at),
            'details'       => $message->sms_bank
            
        ];

        return response()->json(['status' => true, 'data' => $response]);
    }

    public function save(CreateMessageRequest $request)
    {
        $message = Message::create([

            'user_id'           => Auth::user()->id,
            'body'              => request('body'),
            'recipients_type'   => request('recipients_type', 'all'),
            'custom_recipients' => json_encode(request('recipients', null)),
            'scheduled'         => request('scheduled'),
            'schedule_date'     => request('schedule_date', null),
            'schedule_time'     => request('schedule_time', null),
            'repitition_type'   => request('repitition_type', null),
            'repitition_value'  => request('repitition_value', null),
            'sender_name'       => request('sender', null),
        ]);

        return response()->json(['status' => true, 'data' => $message], 201);
    }

    public function deleteMessage(Request $request, $id = 0)
    {
        $id = request('id', $id);

        if(!$id) return response()->json(['status' => false, 'data' => 'Message ID is required'], 400);

        $message = Message::where(['user_id' => Auth::user()->id, 'id' => $id])->first();

        if(!$message) return response()->json(['status' => false, 'data' => 'Message Not Found'], 404);

        $message->delete();

        return response()->json(['status' => true, 'data' => 'Message Deleted Succesfully']);

    }
}
