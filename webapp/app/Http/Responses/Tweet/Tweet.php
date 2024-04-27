<?php

namespace App\Http\Responses\Tweet;

use App\Entities\TweetType;
use Illuminate\Contracts\Support\Arrayable;
use App\Http\Responses\User;

class Tweet implements Arrayable
{
    public static function create(\App\Services\Tweet\Tweet $tweet): static
    {
        return new static(
            $tweet->entity->id->value(),
            User::create($tweet->user),
            $tweet->entity->type,
            $tweet->entity->text,
            $tweet->entity->created_at,
            $tweet->entity->updated_at,
        );
    }

    public function __construct(
        private readonly int $id,
        private readonly User $user,
        private readonly TweetType $tweet_type,
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
            'tweet_type' => $this->tweet_type->value,
            'user' => $this->user->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
