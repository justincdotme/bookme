<?php

Auth::routes();

Route::get('/properties/{id}', 'PropertyController@show');

Route::post('/properties/{id}/reservations', 'PropertyReservationController@store');

Route::post('/properties/{id}/reservations/check', 'ReservationCheckController@show');

Route::post('/properties', 'PropertyController@store')->middleware('can:create,App\Core\Property');

Route::put('/properties/{id}', 'PropertyController@update')->middleware('can:update,App\Core\Property');