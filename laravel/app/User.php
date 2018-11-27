<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;
    
    protected $guarded  = ['deleted_at'];
    protected $dates    = ['deleted_at'];
    
    protected $hidden = ['password', 'remember_token', 'deleted_at', 'updated_at'];

    public function groups()
    {
        return $this->hasMany('\App\Group');
    }

    public function age_brackets()
    {
        return $this->hasMany('\App\AgeBracket');
    }

    public function contacts()
    {
        return $this->hasMany('\App\Contact');
    }

    public function setting()
    {
        return $this->hasOne('\App\Setting');
    }
}
