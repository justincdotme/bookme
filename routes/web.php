<?php

Auth::routes();

Route::get('/properties/{id}', 'PropertyController@show');

Route::post('/properties/{id}/reservations', 'PropertyReservationController@store');

Route::post('/properties/{id}/reservations/check', 'ReservationCheckController@show');

//TODO - We are only checking for admin privileges here.
//TODO - Perhaps a route group that checks isAdmin via middleware would be more appropriate.
Route::post('/properties', 'PropertyController@store')
    ->middleware('can:create,App\Core\Property');

Route::put('/properties/{id}', 'PropertyController@update')
    ->middleware('can:update,App\Core\Property');

Route::post('/properties/{id}/photos', 'PropertyImageController@store')
    ->middleware('can:create,App\Core\PropertyImage');