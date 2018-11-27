<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Message;
use App\Schedule;
use DB;

class ScheduleController extends Controller
{
	//Should Run Daily.
	public function run()
	{
		$messages = Message::where(['scheduled' => 1])
		->where('schedule_date', '<=', date('y-m-d'))
		->whereIn('status', ['queued', 'completed'])
		->get();
		
		foreach($messages as $row){

			if($row->repitition_type == 'daily'){

				Schedule::create(['message_id' => $row->id]);
				continue;
			}

			if($row->repitition_type == 'weekly' && $row->repitition_value == date('N')){

				Schedule::create(['message_id' => $row->id]);
				continue;
			}

			if($row->repitition_type == 'monthly' && $row->repitition_value == date('j')){

				Schedule::create(['message_id' => $row->id]);
			}
			
		}
	}
}
