<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Core\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Core\State::class, function (Faker\Generator $faker) {
    return [
        'abbreviation' => $faker->stateAbbr
    ];
});

$factory->define(App\Core\Property::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'rate' => $faker->randomFloat(2),
        'short_description' => $faker->words(3, true),
        'long_description' => $faker->sentences(3, true),
        'street_address_line_1' => $faker->streetAddress,
        'street_address_line_2' => null,
        'city' => $faker->city,
        'state_id' => null,
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