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

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

$factory->define(App\Mail::class, function (Faker\Generator $faker) {
    return [
        "sender" => $faker->name,
        "subject" => $faker->words(2, true),
        "message" => $faker->paragraphs(3, true),
        "time_sent" => $faker->unixTime
    ];
});
