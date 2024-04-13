<?php

namespace App\Http\Responses\Timeline;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use App\Http\Responses\Tweet\Tweet;
use App\Services\Timeline\TimelineContents as Contents;

class TimelineContents implements Arrayable
{
    public static function create(Contents $contents): static
    {
        $tweets = collect([]);
        foreach ($contents->myTweets as $t) {
            $x = Tweet::create($t);
            $tweets->push($x);
        }
        foreach ($contents->followeeTweets as $ts) {
            foreach ($ts as $t) {
                $x = Tweet::create($t);
                $tweets->push($x);
            }
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
        return $this->tweets->map(fn (Tweet $t) => $t->toArray())->toArray();
    }
}
