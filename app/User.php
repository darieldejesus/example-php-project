<?php

namespace App;

use Illuminate\Database\Eloquent\Model as Model;
use DB;
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

    /**
     * Retrieve all users in the area (min/max) of (latitude/longitude)
     *
     * @param array Array with coordinate range.
     * @return Illuminate\Database\Eloquent\Collection Users in the range area.
     */
    public static function getUsersAroundCoordinates($coordinates) {
        if (empty($coordinates)) {
            return FALSE;
        }
        if (!array_get($coordinates, 'latitude.min') ||
            !array_get($coordinates, 'latitude.max') ||
            !array_get($coordinates, 'longitude.min') ||
            !array_get($coordinates, 'longitude.max')) {
            return FALSE;
        }

        // Get each value from given coordinates.
        $latitudeMin = array_get($coordinates, 'latitude.min');
        $latitudeMax = array_get($coordinates, 'latitude.max');
        $longitudeMin = array_get($coordinates, 'longitude.min');
        $longitudeMax = array_get($coordinates, 'longitude.max');

        return User::whereRaw("latitude >= $latitudeMin")
                    ->whereRaw("latitude <= $latitudeMax")
                    ->whereRaw("longitude >= $longitudeMin")
                    ->whereRaw("longitude <= $longitudeMax")
                    ->select('id')
                    ->get();
    }
}
