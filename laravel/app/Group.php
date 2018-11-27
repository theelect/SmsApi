<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
	use SoftDeletes;
	
    protected $guarded = ['deleted_at'];
    protected $dates = ['deleted_at'];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function contacts()
    {
    	return $this->belongsToMany('App\Contact', 'contact_group');
    }
}
