<?php

namespace App\Http\Responses\Tweet;

use Illuminate\Contracts\Support\Arrayable;

class Reply implements Arrayable
{
    public function __construct(
        private readonly User $owner,
        private readonly Tweet $tweet,
    ) {
    }

    public function toArray(): array
    {
        return [
            'owner' => $this->owner->toArray(),
            'tweet' => $this->tweet->toArray(),
        ];
    }
}
