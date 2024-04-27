<?php

namespace App\Http\Responses\Timeline;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use App\Http\Responses\Tweet\Tweet;

class TimelineContents implements Arrayable
{
    public static function create(\App\Services\Timeline\TimelineContents $contents): static
    {
        $tweets = collect([]);
        foreach ($contents->myTweets as $t) {
            $x = Tweet::create($t);
            $tweets->push($x);
        }
        foreach ($contents->followeeTweets as $tweet) {
            $x = Tweet::create($tweet);
            $tweets->push($x);
        }
        return new static($tweets);
    }

    /**
     * @param Collection<Tweet> $tweets
     */
    public function __construct(
        private readonly Collection $tweets,
    ) {
    }

    public function toArray(): array
    {
        return ['tweets' => $this->tweets->map(fn (Tweet $t) => $t->toArray())->toArray()];
    }
}
