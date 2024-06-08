<?php

namespace App\Http\Responses\Tweet;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Tweets implements Arrayable
{
    /**
     * @param Collection<\App\Entities\Tweet> $tweets
     */
    public function __construct(
        private readonly Collection $tweets,
        private readonly ?int $next,
    ) {
    }

    public function toArray(): array
    {
        return [
            'tweets' => $this->tweets->map(fn (\App\Entities\Tweet $tweet) => [
                'id' => $tweet->id->value(),
                'text' => $tweet->text,
                'created_at' => $tweet->created_at,
                'updated_at' => $tweet->updated_at,
            ])->values(),
            'next' => $this->next,
        ];
    }
}
