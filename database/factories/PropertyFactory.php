<?php

$factory->define(App\Core\Property\Property::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'rate' => $faker->randomNumber(),
        'short_description' => $faker->words(3, true),
        'long_description' => $faker->sentences(3, true),
        'street_address_line_1' => $faker->streetAddress,
        'street_address_line_2' => null,
        'city' => strtolower($faker->city),
        'state_id' => 1,
        'zip' => rand(11111, 99999),
        'status' => 'available'
    ];
});

$factory->state(App\Core\Property\Property::class, 'available', function (Faker\Generator $faker) {
    return [
        'status' => 'available'
    ];
});

$factory->state(App\Core\Property\Property::class, 'unavailable', function (Faker\Generator $faker) {
    return [
        'status' => 'unavailable'
    ];
});