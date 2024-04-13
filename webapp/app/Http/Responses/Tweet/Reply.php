<?php

namespace App\Http\Responses\Tweet;

use Illuminate\Contracts\Support\Arrayable;

class Reply implements Arrayable
{
    public static function create(\App\Services\Tweet\Reply $reply): static
    {
        $owner = User::create($reply->owner);
        $tweet = Tweet::create(new \App\Services\Tweet\Tweet($reply->owner, $reply->tweet));
        return new Reply($owner, $tweet);
    }

    public function __construct(
        private readonly User $owner,
        private readonly Tweet $tweet,
    ) {
    }

    public function toArray(): array
    {
        return $this->tweet->toArray();
    }
}
