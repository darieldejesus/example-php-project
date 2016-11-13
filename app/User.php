<?php

namespace App;

use Illuminate\Database\Eloquent\Model as Model;

class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'age',
        'birth_date',
        'host',
        'name',
        'email'
    ];
}
