<?php

Route::get('/properties', 'PropertyController@index');

Route::get('/properties/search', 'PropertySearchController@search');

Route::get('/properties/{property}', 'PropertyController@show');

Route::post('/properties/{property}/reservations/check', 'ReservationCheckController@show');

Route::get('/properties/{property}/images', 'PropertyImageController@index');

Route::group(['middleware' => ['guest']], function () {
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');

    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::get('/register', 'UserController@create')->name('register');

    Route::post('/register', 'UserController@store')->name('register.store');

    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');

    Route::post('/login', 'UserController@login')->name('login.post');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/users/{user}', 'UserController@show');

    Route::put('/users/{user}', 'UserController@update');

    Route::post('/properties/{property}/reservations', 'ReservationController@store');

    Route::get('/users/{user}/reservations/{reservation}', 'UserReservationController@show');

    Route::put('/properties/{property}/reservations/{reservation}', 'ReservationController@update');

    Route::get('/users/{user}/reservations', 'UserReservationController@index');
});

Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {
    Route::get('/properties', 'Admin\PropertyController@index');

    Route::post('/properties', 'Admin\PropertyController@store');

    Route::put('/properties/{property}', 'Admin\PropertyController@update');

    Route::post('/properties/{property}/images', 'Admin\PropertyImageController@store');

    Route::delete('/properties/{property}/images/{image}', 'Admin\PropertyImageController@destroy');

    Route::get('/properties/{property}/reservations/{reservation}', 'Admin\ReservationController@show');

    Route::put('/properties/{property}/reservations/{reservation}', 'Admin\ReservationController@update');
});
