<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgeBracket extends Model
{
    use SoftDeletes;
    
    protected $guarded 	= ['deleted_at'];
    protected $dates 	= ['deleted_at'];

    public function user()
    {
    	return $thsi->belongsTo('App\User');
    }
}
