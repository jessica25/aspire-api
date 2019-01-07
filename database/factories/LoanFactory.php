<?php

use Faker\Generator as Faker;

$factory->define(App\Loan::class, function (Faker $faker) {
    return [
        'amount' => $faker->numberBetween($min = 5000, $max = 100000), 
        'duration' => $faker->numberBetween($min = 1, $max = 6), 
        'repayment_freq' => 'Monthly', 
        'interest_rate' => $faker->randomFloat(2, 1.5, 4), 
        'arr_fee' => $faker->randomFloat(2, 1, 6),
    ];
});

$factory->defineAs(App\Loan::class, 'withUser', function (Faker $faker, int $user) {
    $loan = $factory->raw('App\Loan');
    return array_merge($loan, ['user_id' => $user]);
});