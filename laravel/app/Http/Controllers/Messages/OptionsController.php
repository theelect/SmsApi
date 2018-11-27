<?php

namespace App\Http\Controllers\Messages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Message;
use App\User;
use Auth;
use DB;

class OptionsController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $message = Message::where(['id'=> $id, 'user_id' => _id()])->first();

        if($message != null){

            return view('messages.new.options', ['message' => $message]);
        }
        
        abort(404);
    }

    public function save(Request $request)
    {        

        $message = Message::where(['id' => request('message_id'), 'user_id' => _id()])->first();

        if($message == null){

            abort(404);
        }

        if(request('scheduled') == 1){

            Message::where(['id' => $message->id])->update([

                'scheduled'         => 1,
                'schedule_date'     => date('Y-m-d', strtotime(request('schedule_date'))),
                'schedule_time'     => date('H:i:s', strtotime(request('schedule_time'))),
                'repitition_type'   => request('repitition_type'),
                'repitition_value'  => request(request('repitition_type').'_repitition')
            ]);

        }

        return redirect('message/review/'.$message->id.'/'.md5(time()));

    }
}
