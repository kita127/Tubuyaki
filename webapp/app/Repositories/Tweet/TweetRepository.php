<?php

namespace App\Repositories\Tweet;

use App\Entities\Tweet;
use Illuminate\Support\Collection;

interface TweetRepository
{
    /**
     * @param Tweet $tweet
     * @return Tweet
     */
    public function save(Tweet $tweet): Tweet;

    /**
     * @param array<string, mixed> $where
     * @return Collection<int, Tweet>    key:ID
     */
    public function findAllBy(array $where): Collection;
}