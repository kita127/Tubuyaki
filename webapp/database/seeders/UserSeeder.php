<?php

namespace Database\Seeders;

use App\Models\Tweet;
use App\Models\TweetDetail;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->has(
            Tweet::factory()->has(
                TweetDetail::factory()
            )->count(3)
        )->count(3)->create();
    }
}
