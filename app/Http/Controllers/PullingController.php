<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;
use App\Contact;
use App\Occupation;
use App\AgeBracket;
use Auth;
use DB;

class PullingController extends Controller
{
    public function run()
    {
        try{

            $setting = Setting::first();

            if(!$setting) return;

            $next_page  = $setting->page + 1;
            $endpoint   = "https://theelect.herokuapp.com/api/v1/contacts?page=$next_page&perPage=500";

            $response = (new \GuzzleHttp\Client())->request('GET', $endpoint)->getBody()->getContents();

            $response = json_decode($response);

            DB::transaction(function () use ($response, $setting, $next_page) {

                if(count($response->docs) == 0){

                    $setting->update(['page' => 0]);  
                    return;           
                }

                $i = 0;

                foreach($response->docs as $row){

                    if(!isset($row->phone)) return;

                    $phone              = _tophone(($row->phone));
                    $occupation_id      = 0;

                    if($row->occupation){

                        $occupation = Occupation::where(['user_id' => 1, 'name' => $row->occupation])->first();

                        if($occupation){

                            $occupation_id = $occupation->id;
                        
                        }else{

                            $occupation = Occupation::create(['user_id' => 1, 'name' => ucfirst($row->occupation)]);

                            $occupation_id = $occupation->id;
                        }
                    }

                    $occupation = Occupation::firstOrNew(['name' => $row->occupation ?? '']);

                    $month = 0;

                    Contact::updateOrCreate(['phone' => $phone],[

                        'user_id'           => 1,
                        'firstname'         => $row->first_name ?? '',
                        'lastname'          => $row->last_name ?? '',
                        'othername'         => $row->other_names ?? '',
                        'phone'             => $phone,
                        'gender'            => $row->gender ? strtolower($row->gender) : '',
                        'age_bracket_id'    => 0,
                        'occupation_id'     => $occupation_id,
                        'vin'               => $row->vin ?? '',
                        'state'             => $row->state_name ?? '',
                        'state_id'          => $row->state_id ?? 0,
                        'local'             => $row->lga ?? '',
                        'ward'              => $row->ward ?? '',
                        'birth_date'        => date('Y-m-d', strtotime($row->birth_date ?? date('Y-m-d'))),
                        'month'             => $month,
                        'language'          => 'english',
                        'gender'            => $row->gender ?? 'none',
                        'status'            => 'active'
                    ]);

                    $i++;
                }

                $setting->update(['page' => $next_page]);

            });

            return response()->json(['status' => true, 'data' => count($response->docs).' Contact(s) Uploaded']);

        }catch(Exception $e){

            return response()->json(['status' => false, 'data' => $e->getMessage()]);

        }
    }
}
