<?php

namespace App\Services\Tweet;

use App\Entities\Tweet as EntitiesTweet;
use App\Services\TubuyakiUser;

class Tweet
{
    public function __construct(
        public readonly TubuyakiUser $user,
        public readonly EntitiesTweet $entity,
    ) {
    }
}
