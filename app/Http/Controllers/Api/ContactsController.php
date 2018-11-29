<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contact;

class ContactsController extends Controller
{
    public function locals()
    {
        $locals = Contact::select('local')->where('local', '!=', null)->groupBy('local')->get();

        $response = [];
        
        foreach($locals as $row){

        	$response[] = ['name' => $row->local];
        }

        return response()->json(['status' => true, 'data' => $response]);
    }

    public function wards()
    {
        $locals = Contact::select('ward')->where('ward', '!=', null)->groupBy('ward')->get();

        $response = [];
        
        foreach($locals as $row){

        	$response[] = ['name' => $row->ward];
        }

        return response()->json(['status' => true, 'data' => $response]);
    }
}
