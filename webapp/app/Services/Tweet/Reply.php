<?php

namespace App\Services\Tweet;

use App\Entities\Tweet as EntitiesTweet;
use App\Services\TubuyakiUser;

class Reply implements Tweet
{
    public function __construct(
        public readonly TubuyakiUser $owner,
        public readonly EntitiesTweet $tweet,
        public readonly Tweet $target,
    ) {
    }
}
