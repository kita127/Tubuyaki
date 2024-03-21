<?php

namespace App\Http\Responses\Tweet;

use Illuminate\Support\Collection;
use App\Http\Responses\Tweet\Reply;
use Illuminate\Contracts\Support\Arrayable;

class Replies implements Arrayable
{
    /**
     * @var Collection<Reply> $replies
     */
    public function __construct(
        private readonly Collection $replies,
    ) {
    }

    public function toArray(): array
    {
        return $this->replies->map(fn (Reply $r) => $r->toArray())->toArray();
    }
}
