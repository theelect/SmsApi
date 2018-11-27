<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    protected $guarded 		= ['updated_at'];

    public function state()
    {
    	return $this->hasMany('App\State');
    }
}
