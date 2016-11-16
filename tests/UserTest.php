<?php

use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    /**
     * @var Mockery $userMock Mock to be used as User model.
     */
    protected $userMock;

    /**
     * Function to set up each test before execute them.
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $this->userMock = Mockery::mock(User::class);
        $this->app->instance(User::class, $this->userMock);
    }

    /**
     * Function to clean resources after each test.
     * @return void
     */
    public function tearDown() {
        Mockery::close();
    }

    /**
     * Function to standard test if user is not found.
     *
     * @param string $url URL where you want to make the request.
     * @param string $method HTTP Method to be used for the request.
     * @param mixed $data Data you want to send in the request.
     * @return void
     */
    private function user_not_found_test($url, $method = 'get', $data = []) {
        $this->userMock
             ->shouldReceive('find')
             ->once()
             ->andReturn(FALSE);

        $responseJSON = [
            'code'     => 400,
            'status'   => 'Bad request',
            'message'  => 'error',
            'response' => [
                'errorCode' => 400,
                'errorMessage' => 'User not found.'
            ]
        ];
        $this->json($method, $url, $data)
            ->seeJsonEquals($responseJSON);
    }

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
    public function test_store_bad_fields()
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
     * Check error is returned when email already exists.
     *
     * @return void
     */
    public function test_store_email_conflict()
    {
        $userData = [
            "first_name" => "John",  
            "last_name"  => "Doe Curtis",
            "age"        => 15,
            "birth_date" => "1979-06-09",
            "name"       => "John Doe",
            "host"       => false,
            "email"      => "test@company.com",  
        ];
  
        $this->userMock
             ->shouldReceive('where')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('first')
             ->once()
             ->andReturn(TRUE);

        $responseJSON = [
            'code'     => 400,
            'status'   => 'Bad request',
            'message'  => 'error',
            'response' => [
                'errorCode' => 400,
                'errorMessage' => 'User email already exists.'
            ]
        ];

        $this->post('/api/v1/users', $userData)
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check user is created.
     *
     * @return void
     */
    public function test_store_create_valid_user()
    {
        $userData = [
            "first_name" => "John",  
            "last_name"  => "Doe Curtis",
            "age"        => 15,
            "birth_date" => "1979-06-09",
            "name"       => "John Doe",
            "host"       => false,
            "email"      => "test@company.com",  
        ];

        $userObject = Mockery::mock('Testing');
        $userObject->shouldReceive('toArray')
                   ->andReturn(['user']);

        $this->userMock
             ->shouldReceive('where')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('first')
             ->once()
             ->andReturn(FALSE)
             ->shouldReceive('create')
             ->once()
             ->andReturn($userObject);

    
        $responseJSON = [
            'code'     => 200,
            'status'   => 'ok',
            'message'  => 'success',
            'response' => ['user']
        ];

        $this->post('/api/v1/users', $userData)
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check user is not found.
     *
     * @return void
     */
    public function test_show_user_not_found()
    {
        $this->user_not_found_test('/api/v1/users/1');
    }

    /**
     * Check user is found.
     *
     * @return void
     */
    public function test_show_user_found()
    {
        $this->userMock
             ->shouldReceive('find')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('toArray')
             ->once()
             ->andReturn(['user']);
    
        $responseJSON = [
            'code'     => 200,
            'status'   => 'ok',
            'message'  => 'success',
            'response' => ['user']
        ];

        $this->get('/api/v1/users/1')
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check update user is not found.
     *
     * @return void
     */
    public function test_update_user_not_found()
    {
        $this->user_not_found_test('/api/v1/users/1', 'put', []);
    }

    /**
     * Check update user data is not valid.
     *
     * @return void
     */
    public function test_update_user_data_not_valid()
    {
        $this->userMock
             ->shouldReceive('find')
             ->once()
             ->andReturn(TRUE);

        $expectedResponse = [
            'code'     => 400,
            'status'   => 'Bad request',
            'message'  => 'error',
            'response' => [
                'errorCode' => 400,
                'errorMessage' => 'Values cannot be empty.'
            ]
        ];
        $this->json('PUT', '/api/v1/users/1', ['first_name' => ''])
             ->seeJsonEquals($expectedResponse);
    }

    /**
     * Check update user email already exists.
     *
     * @return void
     */
    public function test_update_user_email_already_exist()
    {
        $fakeUser = Mockery::mock('UserFake');
        $fakeUser->id = 123;

        $this->userMock
             ->shouldReceive('find')
             ->once()
             ->andReturn(TRUE)
             ->shouldReceive('where')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('first')
             ->once()
             ->andReturn($fakeUser);

        $expectedResponse = [
            'code'     => 400,
            'status'   => 'Bad request',
            'message'  => 'error',
            'response' => [
                'errorCode' => 400,
                'errorMessage' => 'Email already exists.'
            ]
        ];

        $this->json('PUT', '/api/v1/users/321', ['email' => 'test@test'])
             ->seeJsonEquals($expectedResponse);
    }

    /**
     * Check update user not success.
     *
     * @return void
     */
    public function test_update_user_no_success()
    {
        $this->userMock
             ->shouldReceive('find')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('update')
             ->once()
             ->andReturn(FALSE);

        $expectedResponse = [
            'code'     => 400,
            'status'   => 'Bad request',
            'message'  => 'error',
            'response' => [
                'errorCode' => 400,
                'errorMessage' => 'Could not update this user.'
            ]
        ];

        $this->json('PUT', '/api/v1/users/321', ['name' => 'test test'])
             ->seeJsonEquals($expectedResponse);
    }

    /**
     * Check update user success.
     *
     * @return void
     */
    public function test_update_user_success()
    {
        $this->userMock
             ->shouldReceive('find')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('update')
             ->once()
             ->andReturn(TRUE)
             ->shouldReceive('toArray')
             ->once()
             ->andReturn(['user']);

        $expectedResponse = [
            'code'     => 200,
            'status'   => 'ok',
            'message'  => 'updated',
            'response' => ['user']
        ];

        $this->json('PUT', '/api/v1/users/321', ['name' => 'test test'])
             ->seeJsonEquals($expectedResponse);
    }

    /**
     * Check destroy user not found.
     *
     * @return void
     */
    public function test_destroy_user_not_found()
    {
        $this->user_not_found_test('/api/v1/users/1', 'delete');
    }

    /**
     * Check destroy user delete error
     *
     * @return void
     */
    public function test_destroy_user_delete_error()
    {
        $this->userMock
             ->shouldReceive('find')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('delete')
             ->once()
             ->andReturn(FALSE);

        $expectedResponse = [
            'code'     => 400,
            'status'   => 'Bad request',
            'message'  => 'error',
            'response' => [
                'errorCode' => 400,
                'errorMessage' => 'Could not dalete this user.'
            ]
        ];

        $this->json('DELETE', '/api/v1/users/321')
             ->seeJsonEquals($expectedResponse);
    }

    /**
     * Check destroy user delete sucess
     *
     * @return void
     */
    public function test_destroy_user_delete_success()
    {
        $this->userMock
             ->shouldReceive('find')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('delete')
             ->once()
             ->andReturn(TRUE);

        $expectedResponse = [
            'code'     => 200,
            'status'   => 'ok',
            'message'  => 'deleted',
            'response' => true
        ];

        $this->json('DELETE', '/api/v1/users/321')
             ->seeJsonEquals($expectedResponse);
    }

    /**
     * Testing if validation works as expected.
     * 
     * @return void
     */
    public function test_get_user_arround_coordinates_empty_param() {
        $param = [];
        $result = User::getUsersAroundCoordinates($param);
        $this->assertFalse($result);
    }

    /**
     * Testing if validation works as expected.
     * 
     * @return void
     */
    public function test_get_user_arround_coordinates_wrong_param() {
        $param = 'this_is_a_test';
        $result = User::getUsersAroundCoordinates($param);
        $this->assertFalse($result);
    }

    /**
     * Testing if validation works as expected.
     * 
     * @return void
     */
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