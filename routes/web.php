<?php

Route::get('/properties/{id}', 'PropertyController@show');

Auth::routes();
