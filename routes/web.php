<?php

Auth::routes();

Route::get('/properties/{id}', 'PropertyController@show');

Route::post('/properties/{id}/reservations', 'PropertyReservationController@store');

Route::post('/properties/{id}/reservations/check', 'ReservationCheckController@show');

Route::group(['middleware' => ['admin']], function () {
    Route::post('/properties', 'PropertyController@store');

    Route::put('/properties/{id}', 'PropertyController@update');

    Route::post('/properties/{id}/images', 'PropertyImageController@store');
});
