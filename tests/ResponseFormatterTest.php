<?php

use App\Helpers\ResponseFormatter;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ResponseFormatterTest extends TestCase
{
    /**
     * Test default response.
     *
     * @return void
     */
    public function test_default_response()
    {
        $expected = [
            'code'     => 200,
            'status'   => 'OK',
            'message'  => 'success',
            'response' => true
        ];
        $response = new ResponseFormatter;
        $array = $response->toArray();

        $this->assertSame($expected, $array);
    }

    /**
     * Test assigning a code directly.
     *
     * @return void
     */
    public function test_assign_status_code() {
        $statuses = [
            200 => 'OK',
            201 => 'Created',
            400 => 'Bad Request',
            404 => 'Not Found',
            409 => 'Conflict'
        ];
        $response = new ResponseFormatter;
        foreach ($statuses as $code => $status) {
            $array = $response->withCode($code)->toArray();
            $this->assertArrayHasKey('code', $array);
            $this->assertSame($array['code'], $code);
            $this->assertArrayHasKey('status', $array);
            $this->assertSame($array['status'], $status);
        }
    }

    /**
     * Test assigning a code using defined functions.
     *
     * @return void
     */
    public function test_assign_status_code_by_standard() {
        $functions = [
            'withSuccess'    => 200,
            'withCreated'    => 201,
            'withBadRequest' => 400,
            'withNotFound'   => 404,
            'withConflict'   => 409
        ];
        $response = new ResponseFormatter;
        foreach ($functions as $function => $code) {
            $instance = $response->{$function}();
            $this->assertSame($instance->toArray()['code'], $code);
        }
    }

    /**
     * Test assigning response data.
     *
     * @return void
     */
    public function test_assign_response_data_by_construct() {
        $response = new ResponseFormatter();
        $this->assertTrue($response->toArray()['response']);

        $response = new ResponseFormatter([]);
        $this->assertSame($response->toArray()['response'], []);

        $response = new ResponseFormatter(['test']);
        $this->assertSame($response->toArray()['response'], ['test']);

        $response = new ResponseFormatter(false);
        $this->assertSame($response->toArray()['response'], false);

        $response = new ResponseFormatter('this_is_a_test');
        $this->assertSame($response->toArray()['response'], 'this_is_a_test');
    }

    /**
     * Test assigning response data by standard function.
     *
     * @return void
     */
    public function test_assign_response_data_by_function() {
        $response = new ResponseFormatter();

        $response->returnData(true);
        $this->assertTrue($response->toArray()['response']);

        $response->returnData([]);
        $this->assertSame($response->toArray()['response'], []);

        $response->returnData(['test']);
        $this->assertSame($response->toArray()['response'], ['test']);

        $response->returnData(false);
        $this->assertSame($response->toArray()['response'], false);

        $response->returnData('this_is_a_test');
        $this->assertSame($response->toArray()['response'], 'this_is_a_test');
    }

    /**
     * Test assigning message by standard function.
     *
     * @return void
     */
    public function test_assign_message_by_function() {
        $response = new ResponseFormatter();
        $message = 'This is a test';
        $response->withMessage($message);
        $this->assertSame($response->toArray()['message'], $message);
    }
}
