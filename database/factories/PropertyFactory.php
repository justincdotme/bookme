<?php

$factory->define(App\Core\Property::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'rate' => $faker->randomNumber(),
        'short_description' => $faker->words(3, true),
        'long_description' => $faker->sentences(3, true),
        'street_address_line_1' => $faker->streetAddress,
        'street_address_line_2' => null,
        'city' => $faker->city,
        'state_id' => 1,
        'zip' => $faker->postcode,
        'status' => 'available'
    ];
});

$factory->state(App\Core\Property::class, 'available', function (Faker\Generator $faker) {
    return [
        'status' => 'available'
    ];
});

$factory->state(App\Core\Property::class, 'unavailable', function (Faker\Generator $faker) {
    return [
        'status' => 'unavailable'
    ];
});