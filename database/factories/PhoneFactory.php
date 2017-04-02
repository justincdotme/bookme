<?php

$factory->define(App\Core\Phone::class, function (Faker\Generator $faker) {
    return [
        'user_id' => 1,
        'phone' => 1234567890,
    ];
});

$factory->state(App\Core\Phone::class, 'extension', function (Faker\Generator $faker) {
    return [
        'extension' => 123
    ];
});
