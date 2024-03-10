<?php

namespace App\Entities;

use App\Entities\Identifiable\Id;

class Follower extends Entity
{
    public function __construct(
        public readonly Id $id,
        public int $user_id,
        public int $followee_id,
    ) {

    }
}