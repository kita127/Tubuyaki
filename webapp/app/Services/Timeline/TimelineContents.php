<?php

namespace App\Services\Timeline;

use App\Services\Tweet\Tweet;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use LogicException;

class TimelineContents
{
    /**
     * @param Collection<Tweet> $tweets
     * @param ?int $nextIndex
     */
    public function __construct(
        public readonly Collection $tweets,
        public readonly ?int $nextIndex,
    ) {
    }
}
