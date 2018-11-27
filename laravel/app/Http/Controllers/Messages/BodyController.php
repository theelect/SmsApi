<?php

namespace App\Http\Controllers\Messages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Message;
use App\Setting;
use Auth;

class BodyController extends Controller
{
    public function index(Request $request, $id = NULL)
    {
        if($request->isMethod('post')){

            $this->validate($request, [ 'body' => 'required' ]);

            $message = Message::updateOrCreate(['id' => $id], [

                'user_id'       => _id(),
                'body'          => request('body'),
                'sender_name'   => request('sender_name'),
                'translate'     => (bool)request('translate')

            ]);

            return redirect('message/recipients/'.$message->id.'/'.md5(time()));

        }

        $user           = Auth::user(); 
        $message        = Message::firstOrNew(['id' => $id]);
        $sender_name    = $user->name;

        if($message->sender_name == ''){

           if($user->setting){

                $sender_name = $user->setting->sender_name;
           }

        }else{

            $sender_name = $message->sender_name;
        }

        $data['message']        = $message;
        $data['sender_name']    = $sender_name;

        return view('messages.new.body', $data);
    }
}
