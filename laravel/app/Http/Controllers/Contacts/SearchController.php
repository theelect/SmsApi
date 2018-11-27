<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contact;
use App\User;
use App\Group;

class SearchController extends Controller
{
	public function index(Request $request)
	{
		$query = trim(strtolower(request('query')));

		if(strlen($query) < 1)
			return redirect('contacts')->with('error', 'Please include a search query');

		$contacts = [];

		if(intval($query) > 0){

			if($query < 31){

				$contacts = User::find(_id())
				->contacts()->where('day', 'like', '%'.$query.'%')
				->orderBy('firstname')
				->get();

			}else{

				$phone = _tophone($query);
				$contacts = User::find(_id())
				->contacts()->where('phone', 'like', '%'.$phone.'%')
				->orderBy('firstname')
				->get();
			}

		}elseif(filter_var($query, FILTER_VALIDATE_EMAIL)){

			$contacts = User::find(_id())
			->contacts()->where('email', 'like', '%'.$query.'%')
			->orderBy('firstname')
			->get();

		}elseif(in_array($query, ['english', 'yoruba', 'igbo', 'hausa'])){

			$contacts = User::find(_id())
			->contacts()->where('language', 'like', '%'.$query.'%')
			->orderBy('firstname')
			->get();

		}elseif(in_array($query, ['january', 'feburary', 'march', 'april', 'may', 'june', 'july', 'august', 'septempber', 'october', 'november', 'december'])){

			$month  = array_search(ucfirst($query), config('settings.months'));

			$contacts = User::find(_id())
			->contacts()->where('month', 'like', '%'.$month.'%')
			->orderBy('firstname')
			->get();

		}else{

			$parameters = explode(' ', $query);

			if(count($parameters) > 1){

				$contacts = User::find(_id())
				->contacts()
				->where('firstname', 'like', '%'.$parameters[0].'%')
				->orWhere('firstname', 'like', '%'.$parameters[0].'%')
				->orWhere('lastname', 'like', '%'.$parameters[1].'%')
				->orWhere('lastname', 'like', '%'.$parameters[1].'%')
				->orderBy('firstname')
				->get();


			}else{

				$groups = User::find(_id())->groups()->pluck('name')->toArray();

				if(in_array($query, $groups)){

					$group = Group::where(['name' => $query, 'user_id' => _id()])->first();

					if($group != null){

						$contacts = Group::find($group->id)->contacts()->orderBy('firstname')->get();

					}

				}else{

					$contacts = User::find(_id())
					->contacts()
					->where('firstname', 'like', '%'.$query.'%')
					->orWhere('lastname', 'like', '%'.$query.'%')
					->orderBy('firstname')
					->get();
				}


			}
		}

		$data['contacts']         = $contacts;
		$data['query']            = $query;

		return view('contacts.index', $data)->with('status', 'Search Complete.');
	}
}
