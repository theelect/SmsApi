<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $guarded = ['deleted_at'];
    protected $dates = ['deleted_at'];

    public function message()
    {
    	return $this->belongsTo('App\Message');
    }
}
