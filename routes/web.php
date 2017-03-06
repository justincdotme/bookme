<?php

Auth::routes();

Route::get('/properties/{id}', 'PropertyController@show');

Route::post('/properties/{id}/reservations', 'PropertyReservationController@store');
