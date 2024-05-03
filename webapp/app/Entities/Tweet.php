<?php

namespace App\Entities;

use App\Entities\Identifiable\Id;

class Tweet extends Entity
{
    // TODO: readonlyにする
    public function __construct(
        public readonly Id $id,
        public int $user_id,
        public readonly TweetType $type,
        public string $text,
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
