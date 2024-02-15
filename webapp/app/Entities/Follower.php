<?php

namespace App\Entities;

class Follower extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public int $user_id,
        public int $followee_id,
    ) {

    }
}