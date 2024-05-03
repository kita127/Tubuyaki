<?php

namespace App\Services\Tweet;

use App\Services\TubuyakiUser;
use App\Entities\Tweet as EntitiesTweet;

class Retweet implements Tweet
{
    public function __construct(
        public readonly TubuyakiUser $owner,
        public readonly EntitiesTweet $entity,
        public readonly Tweet $target,
    ) {
    }
}
