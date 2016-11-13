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

    /**
     * Reservations that belong to the user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reservations() {
        return $this->hasMany('App\Reservation',
            'user_id_host',
            'id'
        );
    }
}
