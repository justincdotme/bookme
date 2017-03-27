<?php

Auth::routes();

Route::get('/properties', 'PropertyController@index');

Route::get('/properties/{property}', 'PropertyController@show');

Route::post('/properties/{property}/reservations', 'PropertyReservationController@store');

Route::post('/properties/{property}/reservations/check', 'ReservationCheckController@show');

Route::get('/properties/{property}/images', 'PropertyImageController@index');

Route::get('/properties/{property}/reservations/{reservation}', 'PropertyReservationController@show');

Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {
    Route::get('/properties', 'Admin\PropertyController@index');

    Route::post('/properties', 'Admin\PropertyController@store');

    Route::put('/properties/{property}', 'Admin\PropertyController@update');

    Route::post('/properties/{property}/images', 'Admin\PropertyImageController@store');

    Route::delete('/properties/{property}/images/{image}', 'Admin\PropertyImageController@destroy');

    Route::get('/properties/{property}/reservations/{reservation}', 'Admin\ReservationController@show');

    Route::put('/properties/{property}/reservations/{reservation}', 'Admin\ReservationController@update');
});
