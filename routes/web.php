<?php

Auth::routes();

Route::get('/properties', 'PropertyController@index');

Route::get('/properties/{id}', 'PropertyController@show');

Route::post('/properties/{id}/reservations', 'PropertyReservationController@store');

Route::post('/properties/{id}/reservations/check', 'ReservationCheckController@show');

Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {
    Route::get('/properties', 'Admin\PropertyController@index');

    Route::post('/properties', 'Admin\PropertyController@store');

    Route::put('/properties/{id}', 'Admin\PropertyController@update');

    Route::post('/properties/{id}/images', 'Admin\PropertyImageController@store');

    Route::delete('/properties/{id}/images/{imageId}', 'Admin\PropertyImageController@destroy');
});
