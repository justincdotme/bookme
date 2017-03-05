<?php

$factory->define(App\Core\State::class, function (Faker\Generator $faker) {
    return [
        'abbreviation' => $faker->stateAbbr
    ];
});