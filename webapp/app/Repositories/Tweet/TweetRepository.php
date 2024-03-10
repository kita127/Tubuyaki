<?php

namespace App\Repositories\Tweet;

use App\Entities\Tweet;

interface TweetRepository
{
    /**
     * @param Tweet $tweet
     * @return Tweet
     */
    public function save(Tweet $tweet): Tweet;

}