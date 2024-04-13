<?php

namespace App\Services\Timeline;

use App\Services\Tweet\Tweet;
use Illuminate\Support\Collection;

class TimelineContents
{
    /**
     * @param Collection<Tweet> $myTweets
     * @param Collection<Collection<int, Tweet>> $followeeTweets
     */
    public function __construct(
        public readonly Collection $myTweets,
        public readonly Collection $followeeTweets,
    ) {
    }
}
