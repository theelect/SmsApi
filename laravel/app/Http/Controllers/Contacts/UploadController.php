<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Contact;
use App\User;
use App\AgeBracket;
use App\Group;
use DB;
use Auth;
use Validator;
use Illuminate\Validation\Rule;
use App\Rules\PhoneNumberValidationUpload;

class UploadController extends Controller
{
    public function index()
    {
        $data['age_brackets'] = User::find(_id())->age_brackets;
        $data['groups']       = User::find(_id())->groups;
        $data['file_url']     = Storage::url('template.xlsx');
        
        return view('contacts.upload_guide', $data);
    }

    public function store(Request $request)
    {
        if($request->has('upload')){

            $this->validate($request, ['contacts' => 'required|mimes:csv,txt']);

            $path = $request->file('contacts')->store('uploads');

            $file       = fopen(public_path($path), 'r');
            $contacts   = [];

            while(!feof($file)){

                $contacts[] = fgetcsv($file);
            }          

            $errorBag   = [];
            $errorRows  = [];
            $errorCount = $successCount = 0;

            for($i = 1; $i<=count($contacts)-1; $i++){

                $row            = $contacts[$i];

                $firstname      = trim(strtolower($row[0] ?? ''));
                $surname        = trim(strtolower($row[1] ?? ''));
                $phone          = trim(strtolower($row[2] ?? ''));
                $email          = trim(strtolower($row[3] ?? ''));
                $month          = trim(strtolower($row[4] ?? 0));
                $day            = trim(strtolower($row[5] ?? 0));
                $gender         = trim(strtolower($row[6] ?? 'none'));
                $language       = trim(strtolower($row[7] ?? 'english'));
                $age_bracket    = trim(strtolower($row[8] ?? ''));
                $groups         = trim(strtolower($row[9] ?? ''));

                if(empty($phone))
                    continue;
                
                $payload = [

                    'firstname'         => $firstname,
                    'surname'           => $surname,
                    'phone'             => $phone,
                    'email'             => $email,
                    'birth_month'       => $month,
                    'birth_day'         => $day,
                    'age_bracket'       => $age_bracket,
                    'language'          => $language,
                    'gender'            => $gender,
                    'groups'            => $groups,
                    'errors'            => ''
                ];

                $validator = Validator::make($payload, [

                    'firstname'         => 'string',
                    'surname'           => 'string',
                    'phone'             => ['required', 'digits_between:10,11', new PhoneNumberValidationUpload],
                    'email'             => 'email',
                    'birth_month'       => 'numeric|max:12',
                    'birth_day'         => 'numeric|max:31',
                    'age_bracket'       =>  '',
                    'language'          => [Rule::in('english', 'yoruba', 'igbo', 'hausa')],
                    'gender'            => [Rule::in('male', 'female')]
                ]);

                if($validator->fails()){

                    $errors[$i]  = $payload;

                    foreach($validator->errors()->all() as $error){

                        $errors[$i]['errors'] .= "{$error}<br>";
                    }

                     $errorCount++;

                }else{

                    $ageBracket =  AgeBracket::firstOrNew(['user_id' => _id(), 'name' => $age_bracket]);
                    
                    $contact = Contact::create([

                        'user_id'           => _id(),
                        'firstname'         => $firstname,
                        'lastname'          => $surname,
                        'phone'             => config('settings.nigeria_prefix').substr($phone, -10),
                        'email'             => $email,
                        'month'             => $month > 0 ? $month : 0,
                        'day'               => $day >0 ? $day : 0,
                        'age_bracket_id'    => $ageBracket->id,
                        'language'          => $language == '' ? 'english' : $language,
                        'gender'            => $gender == '' ? 'none' : $gender
                    ]);


                    $groups = explode(',', $groups);

                    foreach($groups as $group){

                        $_group = Group::firstOrNew(['user_id' => _id(), 'name' => trim($group) ]);

                        if($_group->id > 0){

                            DB::table('contact_group')->insert([

                                'contact_id'    => $contact->id,
                                'group_id'      => $_group->id
                            ]);
                        }
                    }

                    $successCount++;

                }
            }

            $response = ['errors' => $errors, 'error_count' => $errorCount, 'success_count' => $successCount];
            $total = $errorCount+$successCount;
             _log("Contact(s) Uploaded. [Total Saved : {$successCount}]");
            
            return view('contacts.upload_result', $response)->with('status', "Upload was succesful. {$successCount} of {$total} was succesfully Saved.");
        }
    }
}
