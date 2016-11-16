<?php
namespace App\Helpers;

/**
 * Helper to work with the same standard and prevent duplicate code.
 * Do not repeat yourself.! :)
 *
 * @author Dariel de Jesus <darieldejesus@gmail.com
 * @version 0.1
 */
class ResponseFormatter {

    /** 
     * @var array $responseArray Array which contains response data 
     */
    private $responseArray;

    /**
     * @var array $statusNames Array which has the statuses codes and names.
     */
    private $statusNames = [
        200 => 'OK',
        201 => 'Created',
        400 => 'Bad Request',
        404 => 'Not Found',
        409 => 'Conflict'
    ];

    /**
     * Construct that can receive the data you want to return.
     *
     * @param mixed $responseData Data you want to return.
     * @return void
     */
    public function __construct($responseData = true) {
        // Default response array.
        $this->responseArray = [
            'code'     => 200, // Default response code.
            'status'   => 'OK',
            'message'  => 'success',
            'response' => $responseData
        ];
    }

    /**
     * Given a message, assign it to the array.s
     *
     * @param string $message Message you want to show to the user.
     * @return App\Helpers\ResponseFormatter Instance of this class.
     */
    public function withMessage($message) {
        $this->responseArray['message'] = $message;
        return $this;
    }

    /**
     * Assing what do you want to return in the array.
     *
     * @param mixed $data What do you want to return in 'response'.
     * @return App\Helpers\ResponseFormatter Instance of this class.
     */
    public function withReturnData($data) {
        $this->responseArray['response'] = $data;
        return $this;
    }

    /**
     * Assign Status 200 OK to the response array.
     *
     * @return App\Helpers\ResponseFormatter Instance of this class.
     */
    public function withSuccess() {
        return $this->withCode(200);
    }

    /**
     * Assign Status 201 Created to the response array.
     *
     * @return App\Helpers\ResponseFormatter Instance of this class.
     */
    public function withCreated() {
        return $this->withCode(201);
    }

    /**
     * Assign Status 400 Bad Request to the response array.
     *
     * @return App\Helpers\ResponseFormatter Instance of this class.
     */
    public function withBadRequest() {
        return $this->withCode(400);
    }

    /**
     * Assign Status 404 Not Found to the response array.
     *
     * @return App\Helpers\ResponseFormatter Instance of this class.
     */
    public function withNotFound() {
        return $this->withCode(404);
    }

    /**
     * Assign Status 409 Conflict to the response array.
     *
     * @return App\Helpers\ResponseFormatter Instance of this class.
     */
    public function withConflict() {
        return $this->withCode(409);
    }

    /**
     * Assign a status code to the response array.
     *
     * @param int $statusCode Status code to be assigned to the array.
     * @return App\Helpers\ResponseFormatter Instance of this class.
     */
    public function withCode($statusCode) {
        // If StatusCode does not exists in the list.
        // Keep the array as already is.
        if (!array_key_exists($statusCode, $this->statusNames)) {
            return $this;
        }
        $this->responseArray['status'] = $this->statusNames[$statusCode];
        $this->responseArray['code'] = $statusCode;
        return $this;
    }

    /**
     * Returns the responseArray.
     *
     * @return array The array to be parsed in the response.
     */
    public function toArray() {
        return $this->responseArray;
    }

    /**
     * Returns a response object.
     *
     * @return Illuminate\Http\JsonResponse Parsed response.
     */
    public function parse() {
        return response()->json($this->responseArray, 
                                $this->responseArray['code']);
    }

    /**
     * Returns a new ResponseFormatter instance.
     *
     * @param mixed $data Data you want to return in the response.
     * @return App\Helpers\ResponseFormatter Instance of this class.
     */
    public static function withData($data) {
        $response = new ResponseFormatter();
        $response->withReturnData($data);
        return $response;
    }
}