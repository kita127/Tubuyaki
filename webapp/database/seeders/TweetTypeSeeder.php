<?php

namespace Database\Seeders;

use App\Models\TweetType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TweetTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new TweetType(['value' => 'normal']))->save();
        (new TweetType(['value' => 'retweet']))->save();
        (new TweetType(['value' => 'reply']))->save();
    }
}
