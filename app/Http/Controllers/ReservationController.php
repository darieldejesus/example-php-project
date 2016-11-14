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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'host_id'   => 'required|integer|exists:users,id',
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

        // Fisrt, try to retrieve the given IDs.
        $guests = User::whereIn('id', $data['guest_ids'])
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
        $reservations = Reservation::where('user_id_host', $data['host_id'])
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
        $validator = Validator::make(['id' => $id], [
            'id'   => 'required|integer|exists:reservations,user_id_host'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code'     => 400,
                'status'   => 'Bad request',
                'message'  => 'error',
                'response' => [
                    'errorCode'    => 400,
                    'errorMessage' => 'User or reservation not found.'
                ]
            ], 400);
        }

        $hostReservations = Reservation::where('user_id_host', $id)->get();
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
                'guest'          => $reservation->guest
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
        $validator = Validator::make(['id' => $id], [
            'id'   => 'required|integer|exists:reservations'
        ]);
        if ($validator->fails()) {
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
        $reservation = Reservation::find($id);
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
