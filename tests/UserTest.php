<?php

use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    protected $userMock;

    /**
     * Test to confirm response status code when at least a field is missing. 
     * Also confirm user router exists.
     *
     * @return void
     */
    public function testStoreBadFieldsStatusCode()
    {
        $userNoData = [];

        $this->post('/api/v1/users', $userNoData)
             ->assertResponseStatus(400);
    }

    /**
     * Test to confirm error is returned when at least a field is missing.
     *
     * @return void
     */
    public function testStoreBadFields()
    {
        $userDataWithoutName = [
            "email"      => "test9@company1.com",  
            "first_name" => "John",  
            "last_name"  => "Doe Curtis",
            "age"        => 15,
            "host"       => false,
            "birth_date" => "1979-06-09"
        ];

        $responseJSON = [
            "code"     => 400,
            "status"   => "Bad request",
            "message"  => "error",
            "response" => [
                "errorCode"    => 400,
                "errorMessage" => "Please, check required fields."
            ]
        ];

        $this->post('/api/v1/users', $userDataWithoutName)
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Test to confirm error is returned when a email exists.
     * TODO: Find a way to test Eloquent Models
     *
     * @return void
     */
    // public function testStoreExistingEmail()
    // {
    //     $userDataWithExistingEmail = [
    //         "email"      => "test9@company1.com",
    //         "name"       => "John \"Johnnie\" Doe",  
    //         "first_name" => "John",
    //         "last_name"  => "Doe Curtis",
    //         "age"        => 15,
    //         "host"       => false,
    //         "birth_date" => "1979-06-09"
    //     ];

    //     $this->userMock = Mockery::mock(User::class);
    //     $this->app->instance(User::class, $this->userMock);

    //     $this->userMock->shouldReceive('where')
    //              ->once()
    //              ->andReturn(123);

    //     $responseJSON = [
    //         "code"     => 400,
    //         "status"   => "Bad request",
    //         "message"  => "error",
    //         "response" => [
    //             "errorCode"    => 400,
    //             "errorMessage" => "Bad request"
    //         ]
    //     ];

    //     $this->post('/api/v1/users', $userDataWithExistingEmail)
    //          ->seeJsonEquals($responseJSON);
    // }

    public function test_get_user_arround_coordinates_empty_param() {
        $param = [];
        $result = User::getUsersAroundCoordinates($param);
        $this->assertFalse($result);
    }


    public function test_get_user_arround_coordinates_wrong_param() {
        $param = 'this_is_a_test';
        $result = User::getUsersAroundCoordinates($param);
        $this->assertFalse($result);
    }


    public function test_get_user_arround_coordinates_array_empty_property() {
        $param = [
            'latitude' => [
                'min' => [],
                'max' => []
            ],
            'longitude' => [
                'min' => [],
                'max' => []
            ],
        ];
        $result = User::getUsersAroundCoordinates($param);
        $this->assertFalse($result);
    }
}