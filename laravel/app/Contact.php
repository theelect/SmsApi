<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
	use SoftDeletes;

    protected $guarded = ['deleted_at'];
    protected $dates = ['deleted_at'];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function groups()
    {
    	return $this->belongsToMany('App\Group', 'contact_group');
    }

    public function age_bracket()
    {
        return $this->belongsTo('App\AgeBracket');
    }

    public function state()
    {
        return $this->belongsTo('App\State', 'state_id', 'state_id');
    }

    public function local()
    {
        return $this->belongsTo('App\Local');
    }

    public function upload($path = '')
    {
        if(!file_exists($path))
            return 0;

        try{

            $file       = fopen(public_path($path), 'r');
            $contacts   = [];

            while(!feof($file)){

                $contacts[] = fgetcsv($file);
            }

            dd($contacts);

            
        }catch(Exception $e){


        }
    }
}
