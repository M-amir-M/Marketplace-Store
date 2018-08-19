<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
$factory->define(\App\Role::class,function (Faker $faker){
    return[
        'display_name' => $faker->name,
        'description' => $faker->sentence,
    ];
});

$factory->define(\App\Province::class,function (Faker $faker){
    return[
        'name' => $faker->city
    ];
});

$factory->define(\App\City::class,function (Faker $faker){
    return[
        'name' => $faker->city,
    ];
});

$factory->define(\App\Address::class,function (Faker $faker){
    return [
        'region' => $faker->city,
        'address' => $faker->address,
    ];
});


$factory->define(App\User::class, function (Faker $faker){
    static $password;

    return [
        'name' => $faker->name,
        'code' => str_random(10),
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->unique()->phoneNumber,
        'mobile' => $faker->unique()->phoneNumber,
        'password' => $password ?: $password = bcrypt('123456'),
        'status' => 1,
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Category::class, function (Faker $faker){
    return [
        'parent_id' => rand(0,5),//بیشتر از 5 دسته باشد
        'name' => $faker->name,
        'slug' => $faker->slug,
    ];
});


$factory->define(App\Brand::class, function (Faker $faker){
    return [
        'name' => $faker->company,
        'type' => 'factory',
        'status' => 1,
    ];
});


$factory->define(App\Product::class, function (Faker $faker){
    return [
        'name' => $faker->name,
        'quantity' => rand(50,100),
        'unit' => rand(0,2),
        'amount' => rand(500,1500),
        'price1' => rand(2000,10000),
        'price2' => rand(2000,10000),
        'min_order' => rand(5,10),
        'image' => $faker->imageUrl(),
        'description' => $faker->sentence
    ];
});
