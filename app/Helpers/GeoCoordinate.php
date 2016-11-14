<?php
namespace App\Helpers;

/**
 * Helper to calculate minimun and maximun bouding distance of a coordinate.
 *
 * @see http://janmatuschek.de/LatitudeLongitudeBoundingCoordinates
 *
 * @author Dariel de Jesus <darieldejesus@gmail.com
 * @version 0.1
 */
class GeoCoordinate {

    /**
     * @var int MEAN_RADIUS Earth radius
     */
    const MEAN_RADIUS = 3958.8;

    /**
     * @var int MIN_LAT Minimun latitude (-90 deg) in Radians
     */
    const MIN_LAT = -1.5707963267949;

    /**
     * @var int MAX_LAT Maximun latitude (90 deg) in Radians
     */
    const MAX_LAT = 1.5707963267949;

    /**
     * @var int MIN_LON Minimun longitude (-180 deg) in Radians
     */
    const MIN_LON = -3.1415926535898;

    /**
     * @var int MAX_LON Maximun longitude (180 deg) in Radians
     */
    const MAX_LON = 3.1415926535898;

    /**
     * Given a lat, log and distance (optional) would return
     * two points (minimum and maximun) coordinates.
     *
     * @see http://janmatuschek.de/LatitudeLongitudeBoundingCoordinates
     * @param float $lat Latitude in degree.
     * @param float $long Longitude in degree.
     * @param int|float $dist OPTIONAL distance in miles.
     * @return boolean|array If something is wrong, FALSE is returned.
     *                       Otherwise, an Array with the coordinates.
     */
    public static function getBoundingCoordinates($lat, $long, $dist = 50) {
        if (!is_numeric($lat) || !is_numeric($long)) {
            return FALSE;
        }

        if (abs($lat) > 180 || abs($long) > 180) {
            return FALSE;
        }

        $angularRadius = self::getAngularRadius($dist);
        if (!$angularRadius) {
            return FALSE;
        }

        $latitudeRadian = deg2rad($lat);
        $longitudeRadian = deg2rad($long);

        $latitudeMin = $latitudeRadian - $angularRadius;
        $latitudeMax = $latitudeRadian + $angularRadius;
        $longitudeMin = 0;
        $longitudeMax = 0;
        if ($latitudeMin > self::MIN_LAT && $latitudeMax < self::MAX_LAT) {
            $deltaLongitude = asin(sin($angularRadius) / cos($latitudeRadian));
            $longitudeMin = $longitudeRadian - $deltaLongitude;
            $longitudeMax = $longitudeRadian + $deltaLongitude;
            $pi = pi();
            if ($longitudeMin < self::MIN_LON) {
                $longitudeMin += 2 * $pi;
            }
            if ($longitudeMax > self::MAX_LON) {
                $longitudeMax -= 2 * $pi;
            }
        } else {
            $latitudeMin = max($latitudeMin, self::MIN_LAT);
            $latitudeMax = min($latitudeMax, $self::MAX_LAT);
            $longitudeMin = self::MIN_LON;
            $longitudeMax = self::MAX_LON;
        }

        $latitudeMin = rad2deg($latitudeMin);
        $latitudeMax = rad2deg($latitudeMax);
        $longitudeMin = rad2deg($longitudeMin);
        $longitudeMax = rad2deg($longitudeMax);

        return [
            'latitude' => [
                'min' => $latitudeMin,
                'max' => $latitudeMax
            ],
            'longitude' => [
                'min' => $longitudeMin,
                'max' => $longitudeMax
            ]
        ];
    }

    /**
     * Given a distance in miles, returns the angular radius.
     *
     * @param int|float $dist Distance to be calculated in miles.
     * @return int|float Carculated angular radius.
     */
    public static function getAngularRadius($dist) {
        if (!is_numeric($dist)) {
            return FALSE;
        }
        if (!($dist > 0)) {
            return FALSE;
        }
        return ($dist / self::MEAN_RADIUS);
    }
}
