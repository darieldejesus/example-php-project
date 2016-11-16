<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Reservation;
use App\User;
use Illuminate\Http\Request;
use Validator;

class ReservationController extends Controller
{
    /**
     * @var App\User $User Eloquent model which represent User entity.
     */
    protected $User;

    /**
     * @var App\Reservation $Reservation Eloquent model 
                            which represent Reservation entity.
     */
    protected $Reservation;

    /**
     * Construct to inject models to the class
     * The main purpose of this is unittest :(
     *
     * @param App\User $userModel Model to be used in the class.
     */
    public function __construct(User $user, Reservation $reservation) {
        $this->User = $user;
        $this->Reservation = $reservation;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'host_id'   => 'required|integer',
            'guest_ids' => 'required|array'
        ]);

        // Verify required data is valid.
        if ($validator->fails()) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode'    => 400,
                    'errorMessage' => 'Check required fields and host id.'
                ]
            ], 400);
        }

        $host = $this->User::find($data['host_id']);
        if (!$host) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode'    => 400,
                    'errorMessage' => 'Host not found.'
                ]
            ], 400);
        }

        // Fisrt, try to retrieve the given IDs.
        $guests = $this->User::whereIn('id', $data['guest_ids'])
                        ->select(['id'])
                        ->get();
        // Then, confirm if returned IDs are the same.
        $foundIDs = array_pluck($guests->toArray(), 'id');
        $diff = array_diff($data['guest_ids'], $foundIDs);
        if ($diff) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode'    => 400,
                    'errorMessage' => 'Could not found all guest IDs.'
                ]
            ], 400);
        }

        // Then, we need to confirm reservations does not exists
        // With the same host_id and guest_id
        $reservations = $this->Reservation::where('user_id_host', $data['host_id'])
                                ->whereIn('user_id_guest', $data['guest_ids'])
                                ->get();

        if (!$reservations->isEmpty()) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode'    => 400,
                    'errorMessage' => 'One or more reservations already exist.'
                ]
            ], 400);
        }

        $guests->each(function($guest, $key) use ($data) {
            $newReservation = new Reservation;
            $newReservation->user_id_host = $data['host_id'];
            $newReservation->user_id_guest = $guest['id'];
            $success = $newReservation->save();
        });

        return response()->json([
            'code'     => 200,
            'status'   => 'ok',
            'message'  => 'saved',
            'response' => true
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
        $host = $this->Reservation::where('user_id_host', $id)->first();
        if (!$host) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode'    => 400,
                    'errorMessage' => 'Host not found.'
                ]
            ], 400);
        }

        $hostReservations = $this->Reservation::where('user_id_host', $id)
                                 ->get();
        if ($hostReservations->isEmpty()) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode'    => 400,
                    'errorMessage' => 'No reservation found for this user.'
                ]
            ], 400);
        }

        $guests = [];
        foreach ($hostReservations as $reservation) {
            $guests[] = [
                'reservation_id' => $reservation->id,
                'host'           => $reservation->user_id_host,
                'guest'          => $reservation->user_id_guest
            ];
        }

        return response()->json([
            'code'     => 200,
            'status'   => 'ok',
            'message'  => 'found',
            'response' => [
                'reservations' => $guests
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reservation = $this->Reservation::find($id);
        if (!$reservation) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode'    => 400,
                    'errorMessage' => 'Reservation not found.'
                ]
            ], 400);
        }

        if (!$reservation->delete()) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode' => 400,
                    'errorMessage' => 'Could not dalete this reservation.'
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
}
