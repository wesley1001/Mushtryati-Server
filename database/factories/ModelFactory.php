<?php

$factory->define(\App\Src\User\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'email' => $faker->email,
        'password' => bcrypt('password'),
        'company' => 1,
        'address' => $faker->address,
        'description' => $faker->sentence(10),
        'city' => $faker->city,
        'latitude' =>$faker->randomElement(['29.333298','29.330863','29.327253','29.329760','29.311406']),
        'longitude'=>$faker->randomElement(['47.909263','47.960333','47.993742','48.033611','48.063952']),
        'expire_date' => $faker->dateTime,
        'api_token' => str_random(60),
        'remember_token' => str_random(10),
        'image' => $faker->imageUrl($width = 680, $height = 680),
        'admin' => 0,
        'active' => 1
    ];
});

$factory->define(App\Src\Media\Media::class, function ($faker) {
    return [
        'user_id' => App\Src\User\User::orderByRaw("RAND()")->first()->id,
        'caption' => $faker->sentence(1),
        'url' => $faker->imageUrl($width = 1200, $height = 1200),
        'type' => 'image',
    ];
});

$factory->define(App\Src\Comment\Comment::class, function ($faker) {
    return [
        'user_id' => App\Src\User\User::orderByRaw("RAND()")->first()->id,
        'media_id' => App\Src\Media\Media::orderByRaw("RAND()")->first()->id,
        'comment' => $faker->sentence(1)
    ];
});