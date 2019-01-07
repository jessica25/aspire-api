<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class)->create()->each(function($user) {
            $user->loans()->save(factory(App\Loan::class)->make());
        });
    }
}
