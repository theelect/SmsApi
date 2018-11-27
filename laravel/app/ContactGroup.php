<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactGroup extends Model
{
    protected $table = 'contact_group';
    protected $guarded = ['created_at'];
    public $timestamps = false;
}
