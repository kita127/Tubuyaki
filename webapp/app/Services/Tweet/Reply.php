<?php

namespace App\Services\Tweet;

use App\Entities\Tweet;
use App\Services\TubuyakiUser;

class Reply
{
    public function __construct(
        public readonly TubuyakiUser $owner,
        public readonly Tweet $tweet,
    ) {
    }
}
