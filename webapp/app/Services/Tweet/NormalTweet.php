<?php

namespace App\Services\Tweet;

use App\Entities\Tweet as EntitiesTweet;
use App\Services\TubuyakiUser;

class NormalTweet implements Tweet
{
    public function __construct(
        // TODO: user -> ownerにしたい
        public readonly TubuyakiUser $user,
        public readonly EntitiesTweet $entity,
    ) {
    }
}
