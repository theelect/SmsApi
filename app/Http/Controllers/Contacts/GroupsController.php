<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\User;
use \App\Group;
use Response;
use DB;
use Auth;

class GroupsController extends Controller
{
	public function index(Request $request)
	{	
		if(!empty(request('name'))){

			$name 	= strtolower(request('name'));

			if(User::find(_id())->groups()->where(['name' => $name])->count() < 1){

				Group::create(['user_id' => _id(),  'name' => $name ]);
			}
			
		}

		return Response::json(User::find(_id())->groups, 200);
	}

	public function destroy($id = 0)
	{

		return Response::json(Group::destroy($id), 200);
	}
}
