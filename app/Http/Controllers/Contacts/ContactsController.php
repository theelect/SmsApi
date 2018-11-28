<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contact;
use App\User;
use DB;
use App\Rules\PhoneNumberValidation;

class ContactsController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    return view('contacts.index', ['contacts' => User::find(_id())->contacts()->orderBy('firstname')->paginate(20)]);
  }

  public function contact($id = NULL)
  {
    $data['contact']            = Contact::firstOrNew(['id' => $id]);
    $data['age_brackets']       = User::find(_id())->age_brackets;
    $data['groups']             = User::find(_id())->groups;
    $data['contact_groups']     = Contact::firstOrNew(['id' => $id])->groups()->pluck('groups.id')->toArray();

    return view('contacts.form', $data);
  }

  public function store(Request $request, $id = NULL)
  {
    $contact = Contact::firstOrNew(['id' => $id]);

    $this->validate($request, [

      'firstname'         => 'required|alpha',
      'lastname'          => 'required|alpha',
      'phone'             => ['required', 'digits_between:11,13', new PhoneNumberValidation($contact->phone)],
      'email'             => '',
      'month'             => '',
      'day'               => 'numeric',
      'marital_status'    => '',
      'language'          => '',
      'gender'            => ''

    ]);

    $contact = Contact::updateOrCreate(['id' => $id], [

      'user_id'         => _id(),
      'firstname'       => request('firstname'),
      'lastname'        => request('lastname'),
      'phone'           => _tophone(request('phone')),
      'email'           => request('email'),
      'month'           => request('month'),
      'day'             => request('day'),
      'age_bracket_id'  => request('age_bracket_id'),
      'marital_status'  => request('marital_status'),
      'state_id'        => (int)request('state_id'),
      'local_id'        => request('local_id'),
      'language'        => request('language') ?? 'english',
      'gender'          => request('gender'),
      'status'          => 'active'

    ]);

    $groups    = request('groups') ?? [];
    $_groups   = [];

    DB::table('contact_group')->where(['contact_id' => $contact->id])->delete();

    foreach($groups as $row){

      $_groups[] = ['contact_id' => $contact->id, 'group_id' => $row];
    }

    DB::table('contact_group')->insert($_groups);
    $firstname     = request('firstname');
    $lastname       = request('lastname');
    
    _log("Contact Data Updated. [Name : {$firstname} {$lastname} ]");

    return redirect('contacts')->with('status', 'Your changes have been saved succesfuly.');
  }

}
