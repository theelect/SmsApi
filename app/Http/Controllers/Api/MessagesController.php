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
        $page       = request('page', 0);
        $user_id    = Auth::user()->id;

        $response = [];

        if ($type == 'all') {

            $messages = Message::by($user_id)->orderBy('id', 'desc');

            if($page > 0)
                $messages->skip($page - 1 * 50)->take(50);

            $response = Message::transform($messages->get());
        }

        if ($type == 'month' || $type == 'year') {

            $messages = Message::by($user_id)
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
