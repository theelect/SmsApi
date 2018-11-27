<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $guarded = ['deleted_at'];
    protected $dates = ['deleted_at'];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public static function balance($user_id = 0)
    {
    	return Transaction::where(['user_id' => $user_id])->sum('quantity');
    }
}
