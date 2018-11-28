<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\Contact;
use App\Transaction;
use App\Log;
use App\State;
use App\Local;

class HomeController extends Controller
{
 
    public function index()
    {
        
        $data['sent']           = Message::where(['user_id' => _id(), 'status' => 'completed', 'scheduled' => 0])->count();
        $data['scheduled']      = Message::where(['user_id' => _id(), 'scheduled' => 1])->count();
        $data['contacts']       = Contact::where(['user_id' => _id()])->count();
        $data['balance']        = Transaction::balance(_id());
        $data['logs']           = Log::where(['user_id' => _id()])->orderBy('created_at', 'desc')->take(30)->get();

        return view('dashboard.index', $data);
    }

    public function helpdesk()
    {
        return view('dashboard.helpdesk');
    }

    public function locals($state_id = 0)
    {   
        $locals = Local::where(['state_id' => $state_id])->get();

        foreach($locals as $row){

            echo "<option value='$row->id'>$row->name</option>";
        }
    }
}
