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

            $messages = Message::where(['user_id' => Auth::user()->id])
                                ->where(function ($query) {
                                    $query->where('status', '=', 'pending')
                                        ->orWhere('status', '=', 'completed');
                                })
                                ->orderBy('id', 'desc');

            if($page > 0)
                $messages->skip($page - 1 * 50)->take(50);

            $messages = $messages->get();

            foreach($messages as $row){

                $response[] = [

                    'id'            => $row->id,
                    'status'        => ($row->status == 'pending') ? 'Scheduled' : 'Sent',
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
                $response[][$key] = $row->toArray();
            }
        }

        return response()->json(['status' => true, 'data' => $response]);
    }

    public function analysis(Request $request)
    {
        $schedule = Message::where(['user_id' => Auth::user()->id, 'status' => 'pending'])
                            ->whereRaw('MONTH(created_at) = ?',[date('m')])
                            ->whereNotNull('schedule_date')
                            ->get()
                            ->count();

        $sent = Message::where(['user_id' => Auth::user()->id, 'status' => 'completed'])
            ->whereRaw('MONTH(created_at) = ?',[date('m')])
            ->whereNull('schedule_date')
            ->get()
            ->count();

        $response = [
            'scheduled' => $schedule,
            'sent' => $sent
        ];

        return response()->json(['status' => true, 'data' => $response]);
    }

}
