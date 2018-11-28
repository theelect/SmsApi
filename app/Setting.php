<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	protected $guarded 	= ['updated_at'];
	
    public function user()
    {
    	return $this->belongsTo('\App\User');
    }
}
