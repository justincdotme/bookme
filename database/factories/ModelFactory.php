<?php

$factory->define(App\Core\State::class, function (Faker\Generator $faker) {
    return [
        'abbreviation' => $faker->stateAbbr
    ];
});

$factory->define(App\Core\Property\PropertyImage::class, function (Faker\Generator $faker) {
    return [
        'property_id' => 1
    ];
});
