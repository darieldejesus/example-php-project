<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Group all API Routes.
Route::group( [ 'prefix' => 'api' ], function() {

	// Group API V1.
	Route::group( [ 'prefix' => 'v1' ], function() {

		// Resource User entity.
		Route::resource( 'users', 'UserController', [
			'only' => [ 'store', 'show', 'update', 'destroy' ]
		] );

		Route::get('users/{id}/reservations', 'ReservationController@show');


		// Resource Reservation.
		Route::resource( 'reservations', 'ReservationController', [
			'only' => [ 'store', 'destroy' ]
		] );
	});

});
