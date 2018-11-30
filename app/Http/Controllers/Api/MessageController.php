<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Message;
use App\CustomizedRecipient;
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
        $recipients = [];

        if(request('recipients')){

            foreach(request('recipients') as $row)
                $recipients[] = _tophone($row);
        }

        $message = Message::create([

            'user_id'           => Auth::user()->id,
            'body'              => request('body'),
            'recipients_type'   => request('recipients_type', 'all'),
            'custom_recipients' => json_encode(array_unique($recipients)),
            'scheduled'         => request('scheduled'),
            'schedule_date'     => request('schedule_date', null),
            'schedule_time'     => request('schedule_time', null),
            'repitition_type'   => request('repitition_type', null),
            'repitition_value'  => request('repitition_value', null),
            'sender_name'       => request('sender', null),
        ]);

        if(request('recipients_type') == 'customize'){

            $customized = CustomizedRecipient::create([

                'message_id'    => $message->id,
                'age_bracket'   => json_encode(request('ages', [])),
                'gender'        => json_encode(request('gender', [])),
                'locals'        => json_encode(request('locals', [])),
                'wards'         => json_encode(request('wards', [])),
                'occupation'    => json_encode(request('occupations', [])),
                'birth_month'   => json_encode(request('months', [])),
            ]);

            $message->update(['customized_recipients_id' => $customized->id]);

        }

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
