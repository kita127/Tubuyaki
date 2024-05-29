<?php

namespace App\Entities;

use App\Entities\Identifiable\Id;

class Tweet extends Entity
{
    public function __construct(
        public readonly Id $id,
        public readonly int $user_id,
        public readonly TweetType $type,
        public readonly string $text,
        public readonly Id $target_id,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array['type'] = $this->type->value;
        $array['target_id'] = $this->target_id->isIdentified() ? $this->target_id->value() : null;
        return $array;
    }
}
