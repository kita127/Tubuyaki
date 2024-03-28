<?php

namespace App\Entities;

use App\Entities\Identifiable\Id;

class Tweet extends Entity
{
    public function __construct(
        public readonly Id $id,
        public int $user_id,
        public readonly TweetType $type,
        public string $text,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {
    }
}
