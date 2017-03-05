<?php

$factory->define(App\Core\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'role_id' => 1,
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Core\Role::class, function (Faker\Generator $faker) {
    return [
        'id' => 1,
        'name' => 'standard'
    ];
});

$factory->state(App\Core\Role::class, 'standard', function (Faker\Generator $faker) {
    return [
        'id' => 1,
        'name' => 'standard'
    ];
});

$factory->state(App\Core\Role::class, 'admin', function (Faker\Generator $faker) {
    return [
        'id' => 2,
        'name' => 'admin'
    ];
});

$factory->state(App\Core\User::class, 'standard', function (Faker\Generator $faker) {
    return [
        'role_id' => '1'
    ];
});

$factory->state(App\Core\User::class, 'admin', function (Faker\Generator $faker) {
    return [
        'role_id' => '2'
    ];
});