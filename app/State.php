<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $guarded 		= ['updated_at'];

    public function contacts()
    {
    	return $this->hasMany('App\Contact');
    }

    public function country()
    {
    	return $this->belongsTo('App\Country');
    }

    public function locals()
    {
    	return $this->hasMany('App\Local');
    }
}
