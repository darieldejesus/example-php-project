<?php

use App\Helpers\GeoCoordinate;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GeoCoordinateTest extends TestCase
{
    /**
     * Test if getAngularRadius works as expected.
     *
     * @return void
     */
    public function test_get_angular_radius()
    {
        $expected = 0.01263008992624;
        $distance = 50;
        $angular = GeoCoordinate::getAngularRadius($distance);
        $this->assertSame($expected, $angular);

        $distance = '50';
        $angular = GeoCoordinate::getAngularRadius($distance);
        $this->assertSame($expected, $angular);
    }

    /**
     * Test if getAngularRadius validation works as expected.
     *
     * @return void
     */
    public function test_get_angular_radius_wrong_distance()
    {
        $valuesToTest = [
            0,
            -50,
            'abc',
            '1abc',
            NULL,
            true,
            false
        ];
        foreach ($valuesToTest as $value) {
            $angular = GeoCoordinate::getAngularRadius($value);
            $this->assertFalse($angular);
        }
    }

    /**
     * Test if getBoundingCoordinates works as expected.
     *
     * @return void
     */
    public function test_get_bounding_coordinates()
    {
        $expected = [
            'latitude'  => [
                'min' => 19.016737491413441,
                'max' => 19.885118508586562
            ],
            'longitude' => [
                'min' => -71.155222498194206,
                'max' => -70.234279501805801
            ]
        ];
        $lat = 19.450928;
        $lon = -70.694751;
        $dist = 30;
        $coordinates = GeoCoordinate::getBoundingCoordinates($lat, $lon, $dist);
        $this->assertSame($expected, $coordinates);
    }

    /**
     * Test if getAngularRadius validation works as expected.
     *
     * @return void
     */
    public function test_get_bounding_coordinates_wrong_params()
    {
        $valuesToTest = [
            [190, -70, 30],
            [19, -190, 30],
            [19, -70, -30],
            ['190E', -70, 30],
            [19, '-7A0', 30],
            ['-7A0', '-7A0', 30],
            [NULL, NULL, 30]
        ];

        foreach ($valuesToTest as $value) {
            list($lat, $lon, $dist) = $value;
            $result = GeoCoordinate::getBoundingCoordinates($lat, $lon, $dist);
            $this->assertFalse($result);
        }
    }
}
