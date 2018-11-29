<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Message;
use Carbon\Carbon;

class MessagesController extends Controller
{
    public function index(Request $request, $type = '')
    {
        $page = request('page', 0);

        $response = [];

        if ($type == 'all') {

            $messages = Message::where(['user_id' => Auth::user()->id])->orderBy('id', 'desc');

            if($page > 0)
                $messages->skip($page - 1 * 50)->take(50);

            $messages = $messages->get();

            foreach($messages as $row){

                $response[] = [

                    'id'            => $row->id,
                    'status'        => strtoupper($row->status),
                    'scheduled'     => $row->scheduled,
                    'recipients'    => number_format($row->sms_bank->count()),
                    'body'          => $row->body,
                    'sender'        => $row->sender_name,
                    'cost'          => number_format($row->sms_units * 4),
                    'date'          => _date($row->created_at, true),

                ];
            }
        }

        if ($type == 'month' || $type == 'year') {

            $messages = Message::where(['user_id' => Auth::user()->id])
                ->orderBy('id', 'desc')
                ->get()
                ->groupBy(function($val) use ($type) {
                    return ($type == 'month') ? Carbon::parse($val->created_at)->format('M') : Carbon::parse($val->created_at)->format('Y');
                });

            if($page > 0)
                $messages->skip($page - 1 * 50)->take(50);


            foreach($messages as $key => $row){
                $response[$key] = $row->toArray();
            }
        }

        return response()->json(['status' => true, 'data' => $response]);
    }

}
