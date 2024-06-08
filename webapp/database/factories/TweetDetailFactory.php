<?php

namespace Database\Factories;

use App\Models\TweetType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TweetDetail>
 */
class TweetDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'tweet_type_id' => TweetType::query()->where('value', 'normal')->first()->id,
            'text' => fake()->realText(140),
        ];
    }
}
