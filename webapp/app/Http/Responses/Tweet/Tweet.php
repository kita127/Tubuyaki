<?php

namespace App\Http\Responses\Tweet;

use Illuminate\Contracts\Support\Arrayable;

class Tweet implements Arrayable
{
    public static function create(\App\Entities\Tweet $tweet): static
    {
        return new Tweet(
            $tweet->id->value(),
            $tweet->text,
            $tweet->created_at,
            $tweet->updated_at,
        );
    }

    public function __construct(
        private readonly int $id,
        private readonly string $text,
        private readonly string $created_at,
        private readonly string $updated_at,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
