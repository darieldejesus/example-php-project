<?php

namespace App\Http\Controllers;

use App\Helpers\GeoCoordinate;
use App\Http\Requests;
use App\User;
use Illuminate\Http\Request;
use Validator;
use DB;
/**
 * Class to handle Users.
 */
class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'first_name' => 'required',
            'last_name'  => 'required',
            'age'        => 'required',
            'birth_date' => 'required',
            'name'       => 'required',
            'host'       => 'required',
            'email'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode' => 400,
                    'errorMessage' => 'Please, check required fields.'
                ]
            ], 400);
        }

        $exists = User::where('email', $data['email'])->first();

        if ($exists) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode' => 400,
                    'errorMessage' => 'User email already exists.'
                ]
            ], 400);
        }

        $user = User::create($data);
        return response()->json([
            'code'     => 200,
            'status'   => 'ok',
            'message'  => 'success',
            'response' => $user->toArray()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find((int) $id);
        if (!$user) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode' => 400,
                    'errorMessage' => 'User not found.'
                ]
            ], 400);
        }

        return response()->json([
            'code'     => 200,
            'status'   => 'ok',
            'message'  => 'success',
            'response' => $user->toArray()
        ]);
    }

    /**
     * Update a user entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id User ID to be updated.
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find((int) $id);
        if (!$user) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode' => 400,
                    'errorMessage' => 'User not found.'
                ]
            ], 400);
        }

        $data = $request->all();
        $validator = Validator::make($data, [
            'first_name' => 'filled',
            'last_name'  => 'filled',
            'age'        => 'filled',
            'birth_date' => 'filled',
            'name'       => 'filled',
            'host'       => 'filled',
            'email'      => 'filled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode' => 400,
                    'errorMessage' => 'Values cannot be empty.'
                ]
            ], 400);
        }

        if (array_key_exists('email', $data)) {
            $exists = User::where('email', $data['email'])->first();
            if ($exists && $exists->id != $id) {
                return response()->json([
                    'code'     => 400,
                    'status'   => 'Bad request',
                    'message'  => 'error',
                    'response' => [
                        'errorCode' => 400,
                        'errorMessage' => 'Email already exists.'
                    ]
                ], 400);
            }
        }

        $processed = $user->update($data);
        if ($processed !== TRUE) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode' => 400,
                    'errorMessage' => 'Could not update this user.'
                ]
            ], 400);
        } else {
            return response()->json([
                'code'     => 200,
                'status'   => 'ok',
                'message'  => 'updated',
                'response' => $user->toArray()
            ]);
        }
    }

    /**
     * Remove an user by Id
     *
     * @param  int $id User ID to be deleted.
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find((int) $id);
        if (!$user) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode' => 400,
                    'errorMessage' => 'User not found.'
                ]
            ], 400);
        }

        $deleted = $user->delete();
        if ($deleted !== TRUE) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode' => 400,
                    'errorMessage' => 'Could not dalete this user.'
                ]
            ], 400);
        } else {
            return response()->json([
                'code'     => 200,
                'status'   => 'ok',
                'message'  => 'deleted',
                'response' => true
            ]);
        }
    }

    /**
     * Get users arround range of a given user.
     *
     * @param  int $id User ID to be consulted.
     * @return \Illuminate\Http\Response
     */
    public function recommendation($id) {
        $validator = Validator::make(['id' => $id], [
            'id'   => 'required|integer|exists:users'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode'    => 400,
                    'errorMessage' => 'User not found.'
                ]
            ], 400);
        }
        // Get the user and its coordinate.
        $user = User::find($id);
        $lat = $user->latitude;
        $lon = $user->longitude;

        // After obtain the coordinates range (min-max),
        // Get the users by this range.
        $coordinates = GeoCoordinate::getBoundingCoordinates($lat, $lon);

        $users = User::getUsersAroundCoordinates($coordinates);
        if ($users->isEmpty()) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode'    => 400,
                    'errorMessage' => 'No users in the range.'
                ]
            ], 400);
        }
        // Extracts IDs from result array.
        $userIds = array_pluck($users->toArray(), 'id');
        return response()->json([
            'code'     => 200,
            'status'   => 'ok',
            'message'  => 'found',
            'response' => ['user_ids' => $userIds]
        ]);
    }
}
