<?php

namespace App\Http\Responses\Tweet;

use Illuminate\Contracts\Support\Arrayable;

class Tweet implements Arrayable
{
    public static function create(\App\Services\Tweet\Tweet $tweet): static
    {
        return new static(
            $tweet->entity->id->value(),
            User::create($tweet->user),
            $tweet->entity->text,
            $tweet->entity->created_at,
            $tweet->entity->updated_at,
        );
    }

    public function __construct(
        private readonly int $id,
        private readonly User $user,
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
            'user' => $this->user->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
