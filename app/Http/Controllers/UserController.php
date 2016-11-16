<?php

namespace App\Http\Controllers;

use App\Helpers\GeoCoordinate;
use App\Helpers\ResponseFormatter as RF;
use App\Http\Requests;
use App\User;
use Illuminate\Http\Request;
use Validator;
/**
 * Class to handle Users.
 */
class UserController extends Controller
{

    /**
     * @var App\User $User Eloquent model which represent User entity.
     */
    protected $User;

    /**
     * Construct to inject models to the class
     * The main purpose of this is unittest :(
     *
     * @param App\User $userModel Model to be used in the class.
     */
    public function __construct(User $userModel) {
        $this->User = $userModel;
    }
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
            return RF::withData(false)
                     ->withBadRequest()
                     ->withMessage('Please, check required fields.')
                     ->parse();
         }

        $exists = $this->User::where('email', $data['email'])->first();
        if ($exists) {
            return RF::withData(false)
                     ->withConflict()
                     ->withMessage('User email already exists.')
                     ->parse();
        }

        $user = $this->User::create($data);
        return RF::withData($user->toArray())
                 ->parse();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->User::find((int) $id);
        if (!$user) {
            return RF::withData(false)
                     ->withBadRequest()
                     ->withMessage('User not found.')
                     ->parse();
        }

        return RF::withData($user->toArray())
                 ->parse();
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
        $user = $this->User::find((int) $id);
        if (!$user) {
            return RF::withData(false)
                     ->withBadRequest()
                     ->withMessage('User not found.')
                     ->parse();
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
            return RF::withData(false)
                     ->withBadRequest()
                     ->withMessage('Please, verify required fields.')
                     ->parse();
        }

        if (array_key_exists('email', $data)) {
            $exists = $this->User::where('email', $data['email'])->first();
            if ($exists && $exists->id != $id) {
                return RF::withData(false)
                     ->withConflict()
                     ->withMessage('User email already exists.')
                     ->parse();
            }
        }

        $processed = $user->update($data);
        if ($processed !== TRUE) {
            return RF::withData(false)
                     ->withBadRequest()
                     ->withMessage('Could not update this user.')
                     ->parse();
        } else {
            return RF::withData($user->toArray())
                     ->parse();
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
        $user = $this->User::find((int) $id);
        if (!$user) {
            return RF::withData(false)
                     ->withBadRequest()
                     ->withMessage('User not found.')
                     ->parse();
        }

        $deleted = $user->delete();
        if ($deleted !== TRUE) {
            return RF::withData(false)
                     ->withBadRequest()
                     ->withMessage('Could not delete this user.')
                     ->parse();
        } else {
            return RF::withData(true)
                     ->parse();
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
            return RF::withData(false)
                     ->withBadRequest()
                     ->withMessage('User not found.')
                     ->parse();
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
            return RF::withData(false)
                     ->withBadRequest()
                     ->withMessage('No data in range.')
                     ->parse();
        }
        // Extracts IDs from result array.
        $userIds = array_pluck($users->toArray(), 'id');
        return RF::withData(['user_ids' => $userIds])
                 ->parse();
    }
}
