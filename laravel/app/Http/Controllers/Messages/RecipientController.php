<?php

namespace App\Http\Controllers\Messages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Message;
use Auth;
use DB;

class RecipientController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $message = Message::where(['id'=> $id])->first();

        if($message != null && $message->user->id == _id()){

            $data['message_id']         = $id;
            $data['age_brackets']       = Auth::user()->age_brackets;
            $data['groups']             = Auth::user()->groups;
            $data['states']             = DB::table('states')->get();

            return view('messages.new.recipients', $data);
        }

        abort(404);
    }

    public function save(Request $request)
    {
        $recipients = request('recipients');

        $message = Message::where(['id' => request('message_id')])->first();

        if($message == null)
            abort(404);

        if($recipients == 'all'){

            Message::where(['id' => $message->id])->update(['recipients_type' => 'all']);
        }

        if($recipients == 'customize'){

            $customize_id = DB::table('customized_recipients')->insertGetId([

                'message_id'        => $message->id,
                'age_bracket'       => json_encode(request('age_bracket')),
                'gender'            => json_encode(request('gender')),
                'marital_status'    => json_encode(request('marital_status')),
                'birth_month'       => json_encode(request('months')),
                'state'             => json_encode(request('states')),
                'group'             => json_encode(request('groups')),
                'name'              => request('selection_name'),
                'created_at'        => date('Y-m-d H:i:s')

            ]);

            Message::where(['id' => $message->id])->update([

                'recipients_type'           => 'customize', 
                'customized_recipients_id'  => $customize_id

            ]);
        }

        if($recipients == 'custom'){

            Message::where(['id' => $message->id])->update([

                'recipients_type'           => 'custom', 
                'custom_recipients'         => json_encode(explode(',', request('custom_recipients')))
                
            ]);
        }

        return redirect('message/options/'.$message->id.'/'.md5(time()));
    }
}
