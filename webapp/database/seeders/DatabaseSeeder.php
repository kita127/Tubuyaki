<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Exception;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        if (app()->isLocal()) {
            $this->call([
                UserSeeder::class,
                UserDetailSeeder::class,
                TweetTypeSeeder::class,
            ]);
        } elseif (app()->runningUnitTests()) {
            $this->call([
                TweetTypeSeeder::class,
            ]);
        } else {
            throw new Exception('意図しない環境です');
        }
    }
}
