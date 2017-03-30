<?php


$factory->define(App\Core\Role::class, function (Faker\Generator $faker) {
    return [];
});

$factory->state(App\Core\Reservation::class, 'standard', function (Faker\Generator $faker) {
    return [
        'id' => 1,
        'name' => 'standard'
    ];
});

$factory->state(App\Core\Reservation::class, 'admin', function (Faker\Generator $faker) {
    return [
        'id' => 2,
        'status' => 'admin'
    ];
});