<?php

$factory->define(App\Core\Property\PropertyImage::class, function (Faker\Generator $faker) {
    return [
        'thumb_path' => $faker->image('/tmp'),
        'full_path' => $faker->image('/tmp')
    ];
});

