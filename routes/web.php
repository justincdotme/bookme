<?php

Route::get('/register', [
    'as' => 'register',
    'uses' => 'UserController@create'
])->middleware('guest');

Route::post('/register', [
    'as' => 'register.store',
    'uses' => 'UserController@store'
])->middleware('guest');

Route::get('/login', [
    'as' => 'login',
    'uses' => 'Auth\LoginController@showLoginForm'
]);

Route::post('/login', 'UserController@login');

Route::get('/properties', 'PropertyController@index');

Route::get('/properties/{property}', 'PropertyController@show');

Route::post('/properties/{property}/reservations', 'ReservationController@store');

Route::post('/properties/{property}/reservations/check', 'ReservationCheckController@show');

Route::get('/properties/{property}/images', 'PropertyImageController@index');

Route::get('/properties/{property}/reservations/{reservation}', 'ReservationController@show');

Route::put('/properties/{property}/reservations/{reservation}', 'ReservationController@update');

Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {
    Route::get('/properties', 'Admin\PropertyController@index');

    Route::post('/properties', 'Admin\PropertyController@store');

    Route::put('/properties/{property}', 'Admin\PropertyController@update');

    Route::post('/properties/{property}/images', 'Admin\PropertyImageController@store');

    Route::delete('/properties/{property}/images/{image}', 'Admin\PropertyImageController@destroy');

    Route::get('/properties/{property}/reservations/{reservation}', 'Admin\ReservationController@show');

    Route::put('/properties/{property}/reservations/{reservation}', 'Admin\ReservationController@update');
});
