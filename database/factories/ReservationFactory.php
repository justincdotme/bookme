<?php

use Carbon\Carbon;

$factory->define(App\Core\Reservation::class, function (Faker\Generator $faker) {
    return [
        'property_id' => 1,
        'user_id' => 1,
        'status' => 'pending',
        'date_start' => Carbon::now(),
        'date_end' => Carbon::parse('+1 week'),
    ];
});

$factory->state(App\Core\Reservation::class, 'pending', function (Faker\Generator $faker) {
    return [
        'status' => 'pending'
    ];
});

$factory->state(App\Core\Reservation::class, 'confirmed', function (Faker\Generator $faker) {
    return [
        'status' => 'confirmed'
    ];
});

$factory->state(App\Core\Reservation::class, 'paid', function (Faker\Generator $faker) {
    return [
        'status' => 'paid'
    ];
});

$factory->state(App\Core\Reservation::class, 'cancelled', function (Faker\Generator $faker) {
    return [
        'status' => 'cancelled'
    ];
});