<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Shoe;
use Faker\Generator as Faker;

$factory->define(Shoe::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->text,
        'price' => rand(500, 8000),
        'color' => $faker->colorName,
        'size' => $faker->numberBetween($min = 1, $max = 48),
        'status' => $faker->boolean
    ];
});
