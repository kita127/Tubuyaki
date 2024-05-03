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
        return $tweet->createResponse();
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
