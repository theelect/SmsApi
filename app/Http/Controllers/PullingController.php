<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;
use App\Contact;
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

                    $phone = _tophone(($row->phone));

                    Contact::updateOrCreate(['phone' => $phone],[

                        'user_id'       => 1,
                        'firstname'     => $row->first_name ?? '',
                        'lastname'      => $row->last_name ?? '',
                        'othername'     => $row->other_names ?? '',
                        'phone'         => $phone,
                        'gender'        => $row->gender ?? '',
                        'occupation'    => $row->occupation ?? '',
                        'vin'           => $row->vin ?? '',
                        'state'         => $row->state_name ?? '',
                        'state_id'      => $row->state_id ?? 0,
                        'local'         => $row->lga ?? '',
                        'ward'          => $row->ward ?? '',
                        'language'      => 'english',
                        'gender'        => $row->gender ?? 'none',
                        'status'        => 'active'
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
