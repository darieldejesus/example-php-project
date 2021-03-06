<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id_host',
        'user_id_guest'
    ];

    public function guest() {
        return $this->hasOne('App\User', 'id', 'user_id_guest');
    }
}
