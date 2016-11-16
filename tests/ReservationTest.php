<?php

use App\User;
use App\Reservation;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReservationTest extends TestCase
{
    /**
     * @var Mockery $userMock Mock to be used as User model.
     */
    protected $userMock;

    /**
     * @var Mockery $reservationMock Mock to be used as Reservation model.
     */
    protected $reservationMock;

    /**
     * Function to set up each test before execute them.
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $this->userMock = Mockery::mock(User::class);
        $this->reservationMock = Mockery::mock(Reservation::class);
        $this->app->instance(User::class, $this->userMock);
        $this->app->instance(Reservation::class, $this->reservationMock);
    }

    /**
     * Function to clean resources after each test.
     * @return void
     */
    public function tearDown() {
        Mockery::close();
    }

    /**
     * Check validation
     *
     * @return void
     */
    public function test_store_reservation_wrong_data()
    {
        $reservationData = [
            "host_id"  => 1,
            "guest_id" => 2 // Expects array.
        ];

        $responseJSON = [
            "code"     => 400,
            "status"   => "Bad request",
            "message"  => "error",
            "response" => [
                "errorCode"    => 400,
                "errorMessage" => "Check required fields and host id."
            ]
        ];

        $this->post('/api/v1/reservations', $reservationData)
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check validation host not found.
     *
     * @return void
     */
    public function test_store_reservation_host_not_found()
    {
        $reservationData = [
            "host_id"  => 1,
            "guest_ids" => [2, 3]
        ];

        $this->userMock
             ->shouldReceive('find')
             ->once()
             ->andReturn(FALSE);

        $responseJSON = [
            "code"     => 400,
            "status"   => "Bad request",
            "message"  => "error",
            "response" => [
                "errorCode"    => 400,
                "errorMessage" => "Host not found."
            ]
        ];

        $this->post('/api/v1/reservations', $reservationData)
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check validation guests not found (at least one).
     *
     * @return void
     */
    public function test_store_reservation_guests_not_found()
    {
        $guests = [
            ['id' => 2],
            ['id' => 3],
            ['id' => 4]
        ];

        $reservationData = [
            "host_id"  => 1,
            "guest_ids" => [2, 3, 4, 5]
        ];

        $this->userMock
             ->shouldReceive('find')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('whereIn')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('select')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('get')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('toArray')
             ->once()
             ->andReturn($guests);

        $responseJSON = [
            "code"     => 400,
            "status"   => "Bad request",
            "message"  => "error",
            "response" => [
                "errorCode"    => 400,
                "errorMessage" => "Could not found all guest IDs."
            ]
        ];

        $this->post('/api/v1/reservations', $reservationData)
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check combination Host/Guest does not exist.
     *
     * @return void
     */
    public function test_store_reservation_guests_does_not_exist()
    {
        $guests = [
            ['id' => 2],
            ['id' => 3],
            ['id' => 4]
        ];

        $reservationData = [
            "host_id"  => 1,
            "guest_ids" => [2, 3, 4]
        ];

        $this->userMock
             ->shouldReceive('find')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('whereIn')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('select')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('get')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('toArray')
             ->once()
             ->andReturn($guests);

        $this->reservationMock
             ->shouldReceive('where')
             ->once()
             ->andReturn($this->reservationMock)
             ->shouldReceive('whereIn')
             ->once()
             ->andReturn($this->reservationMock)
             ->shouldReceive('get')
             ->once()
             ->andReturn($this->reservationMock)
             ->shouldReceive('isEmpty')
             ->once()
             ->shouldReceive(FALSE);

        $responseJSON = [
            "code"     => 400,
            "status"   => "Bad request",
            "message"  => "error",
            "response" => [
                "errorCode"    => 400,
                "errorMessage" => "One or more reservations already exist."
            ]
        ];

        $this->post('/api/v1/reservations', $reservationData)
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check reservations are saved.
     *
     * @return void
     */
    public function test_store_reservation_guests_done()
    {
        $guests = [
            ['id' => 2],
            ['id' => 3],
            ['id' => 4]
        ];

        $reservationData = [
            "host_id"  => 1,
            "guest_ids" => [2, 3, 4]
        ];

        $this->userMock
             ->shouldReceive('find')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('whereIn')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('select')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('get')
             ->once()
             ->andReturn($this->userMock)
             ->shouldReceive('toArray')
             ->once()
             ->andReturn($guests)
             ->shouldReceive('each')
             ->once()
             ->andReturn(TRUE);

        $this->reservationMock
             ->shouldReceive('where')
             ->once()
             ->andReturn($this->reservationMock)
             ->shouldReceive('whereIn')
             ->once()
             ->andReturn($this->reservationMock)
             ->shouldReceive('get')
             ->once()
             ->andReturn($this->reservationMock)
             ->shouldReceive('isEmpty')
             ->once()
             ->andReturn(TRUE);

        $responseJSON = [
            'code'     => 200,
            'status'   => 'ok',
            'message'  => 'saved',
            'response' => true
        ];

        $this->post('/api/v1/reservations', $reservationData)
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check reservations show host not found.
     *
     * @return void
     */
    public function test_show_reservation_host_not_found()
    {
        $this->reservationMock
             ->shouldReceive('where')
             ->once()
             ->andReturn($this->reservationMock)
             ->shouldReceive('first')
             ->once()
             ->andReturn(FALSE);

        $responseJSON = [
            'code'     => 400,
            'status'   => 'Bad request',
            'message'  => 'error',
            'response' => [
                'errorCode'    => 400,
                'errorMessage' => 'Host not found.'
            ]
        ];

        $this->get('/api/v1/users/1/reservations')
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check show reservations not found.
     *
     * @return void
     */
    public function test_show_reservation_not_found()
    {
        $this->reservationMock
             ->shouldReceive('where')
             ->twice()
             ->andReturn($this->reservationMock)
             ->shouldReceive('first')
             ->once()
             ->andReturn($this->reservationMock)
             ->shouldReceive('get')
             ->once()
             ->andReturn($this->reservationMock)
             ->shouldReceive('isEmpty')
             ->once()
             ->andReturn(TRUE);

        $responseJSON = [
            'code'     => 400,
            'status'   => 'Bad request',
            'message'  => 'error',
            'response' => [
                'errorCode'    => 400,
                'errorMessage' => 'No reservation found for this user.'
            ]
        ];

        $this->get('/api/v1/users/1/reservations')
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check show reservations are saved.
     *
     * @return void
     */
    public function test_show_reservation_success()
    {
        $firstReservation = (object) [
            'id'            => 1,
            'user_id_host'  => 10,
            'user_id_guest' => 20
        ];
        $secondReservation = (object) [
            'id'            => 2,
            'user_id_host'  => 10,
            'user_id_guest' => 21
        ];
        $reservations = [
            $firstReservation,
            $secondReservation
        ];
        $this->reservationMock
             ->shouldReceive('where')
             ->twice()
             ->andReturn($this->reservationMock)
             ->shouldReceive('first')
             ->once()
             ->andReturn($this->reservationMock)
             ->shouldReceive('get')
             ->once()
             ->andReturn(collect($reservations));

        $responseJSON = [
            'code'     => 200,
            'status'   => 'ok',
            'message'  => 'found',
            'response' => [
                'reservations' => [[
                    'reservation_id' => 1,
                    'host'           => 10,
                    'guest'          => 20
                ],
                [
                    'reservation_id' => 2,
                    'host'           => 10,
                    'guest'          => 21
                ]]
            ]
        ];

        $this->get('/api/v1/users/1/reservations')
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check destroy reservations host not found.
     *
     * @return void
     */
    public function test_destroy_reservation_host_not_found()
    {
        $this->reservationMock
             ->shouldReceive('find')
             ->once()
             ->andReturn(FALSE);

        $responseJSON = [
            'code'     => 400,
            'status'   => 'Bad request',
            'message'  => 'error',
            'response' => [
                'errorCode'    => 400,
                'errorMessage' => 'Reservation not found.'
            ]
        ];

        $this->delete('/api/v1/reservations/1')
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check destroy reservations delete error.
     *
     * @return void
     */
    public function test_destroy_reservation_delete_error()
    {
        $this->reservationMock
             ->shouldReceive('find')
             ->once()
             ->andReturn($this->reservationMock)
             ->shouldReceive('delete')
             ->once()
             ->andReturn(FALSE);

        $responseJSON = [
            'code'     => 400,
            'status'   => 'Bad request',
            'message'  => 'error',
            'response' => [
                'errorCode' => 400,
                'errorMessage' => 'Could not dalete this reservation.'
            ]
        ];

        $this->delete('/api/v1/reservations/1')
             ->seeJsonEquals($responseJSON);
    }

    /**
     * Check destroy reservations delete success.
     *
     * @return void
     */
    public function test_destroy_reservation_delete_success()
    {
        $this->reservationMock
             ->shouldReceive('find')
             ->once()
             ->andReturn($this->reservationMock)
             ->shouldReceive('delete')
             ->once()
             ->andReturn(TRUE);

        $responseJSON = [
            'code'     => 200,
            'status'   => 'ok',
            'message'  => 'deleted',
            'response' => true
        ];

        $this->delete('/api/v1/reservations/1')
             ->seeJsonEquals($responseJSON);
    }
}
