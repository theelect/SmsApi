<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\User;
use \App\AgeBracket;
use Response;
use DB;
use Auth;

class AgeBracketsController extends Controller
{
	public function index(Request $request)
	{
		
		if(!empty(request('name'))){

			$name 	= strtolower(request('name'));

			if(User::find(_id())->age_brackets()->where(['name' => $name])->count() < 1){

				AgeBracket::create(['user_id' => _id(), 'name' => $name ]);

			}
			
		}

		return Response::json(User::find(_id())->age_brackets, 200);
	}

	public function destroy($id = 0)
	{

		return Response::json(AgeBracket::destroy($id), 200);
	}
}
