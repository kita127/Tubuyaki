<?php

namespace App\Http\Responses\Tweet;

use App\Entities\TweetType;
use App\Http\Responses\User;
use Illuminate\Contracts\Support\Arrayable;

class Reply implements Arrayable
{
    public static function create(\App\Services\Tweet\Reply $reply): static
    {
        $owner = User::create($reply->owner);
        $tweet = $reply->tweet;

        return new Reply(
            id: $tweet->id->value(),
            user: $owner,
            tweet_type: $tweet->type,
            target_id: $tweet->target_id->value(),
            text: $tweet->text,
            created_at: $tweet->created_at,
            updated_at: $tweet->updated_at,
        );
    }

    public function __construct(
        private readonly int $id,
        private readonly User $user,
        private readonly TweetType $tweet_type,
        private readonly int $target_id,
        private readonly string $text,
        private readonly string $created_at,
        private readonly string $updated_at,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->toArray(),
            'text' => $this->text,
            'tweet_type' => $this->tweet_type->value,
            'user' => $this->user->toArray(),
            'target_id' => $this->target_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
