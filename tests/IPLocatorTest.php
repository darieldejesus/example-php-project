<?php

use App\Helpers\IPLocator;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IPLocatorTest extends TestCase
{
    /**
     * Test validation: IP is correct.
     *
     * @return void
     */
    public function test_wrong_ip()
    {
        $result = IPLocator::getCoordinateFromIP('192.168.1.');
        $this->assertFalse($result);
    }

    /**
     * Test verify if Helper works and info is returned from external API.
     *
     * @return void
     */
    public function test_ip_locator_request()
    {
        $ip = '8.8.8.8';
        $result = IPLocator::getCoordinateFromIP($ip);
        $this->assertNotEmpty($result);
        $this->assertTrue(is_object($result));
        $this->assertObjectHasAttribute('status', $result);
        $this->assertTrue($result->status == 'success');
    }
}
