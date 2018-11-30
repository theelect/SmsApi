<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contact;
use App\AgeBracket;
use App\Occupation;

class ContactsController extends Controller
{
    public function contactsDefaults()
    {
        $locals = Contact::select('local')->where('local', '!=', null)->groupBy('local')->get();

        $_locals = [];
        
        foreach($locals as $row){

        	$_locals[] = ['name' => $row->local];
        }

        $wards = Contact::select('ward')->where('ward', '!=', null)->groupBy('ward')->get();

        $_wards = [];
        
        foreach($wards as $row){

            $_wards[] = ['name' => $row->ward];
        }

        $ages           = AgeBracket::select(['id', 'name'])->get();
        $occupation     = Occupation::select(['id', 'name'])->get();

        $months = [];

        foreach(_months() as $index => $value){

            $months[] = ['id' => $index, 'name' => $value];
        }

        return response()->json(['status' => true, 'data' => [

            'locals'        => $_locals,
            'wards'         => $_wards,
            'ages'          => $ages,
            'occupations'   => $occupation,
            'months'        => $months
        ]]);
    }
}
