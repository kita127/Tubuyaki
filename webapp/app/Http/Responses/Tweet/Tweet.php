<?php

namespace App\Http\Responses\Tweet;

use App\Entities\TweetType;
use Illuminate\Contracts\Support\Arrayable;
use App\Http\Responses\User;
use App\Services\Tweet\Tweet as TweetTweet;
use LogicException;

class Tweet implements Arrayable
{
    /**
     * 
     * @param TweetTweet $tweet 
     * @return static 
     * @throws LogicException 
     */
    public static function create(\App\Services\Tweet\Tweet $tweet): static
    {
        // TODO: ポリモフィズムで
        return match (true) {
            $tweet instanceof \App\Services\Tweet\NormalTweet => new static(
                $tweet->entity->id->value(),
                User::create($tweet->user),
                $tweet->entity->type,
                $tweet->entity->text,
                null,
                $tweet->entity->created_at,
                $tweet->entity->updated_at,
            ),
            $tweet instanceof \App\Services\Tweet\Reply => new static(
                $tweet->tweet->id->value(),
                User::create($tweet->owner),
                $tweet->tweet->type,
                $tweet->tweet->text,
                $tweet->target->id->value(),
                $tweet->tweet->created_at,
                $tweet->tweet->updated_at,
            ),
            $tweet instanceof \App\Services\Tweet\Retweet => new static(
                $tweet->entity->id->value(),
                User::create($tweet->owner),
                $tweet->entity->type,
                $tweet->entity->text,
                $tweet->target->id->value(),
                $tweet->entity->created_at,
                $tweet->entity->updated_at,
            ),
            default => throw new LogicException(),
        };
    }

    public function __construct(
        private readonly int $id,
        private readonly User $user,
        private readonly TweetType $tweet_type,
        private readonly string $text,
        private readonly ?int $target_id,
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
            'target_id' => $this->target_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
