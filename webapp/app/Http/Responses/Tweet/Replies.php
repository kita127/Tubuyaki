<?php

namespace App\Http\Responses\Tweet;

use Illuminate\Support\Collection;
use App\Http\Responses\Tweet\Reply;
use Illuminate\Contracts\Support\Arrayable;

class Replies implements Arrayable
{
    /**
     * @param Collection<\App\Services\Tweet\Reply>
     */
    public static function create(Collection $replies): static
    {
        $collection = collect([]);
        foreach ($replies as $reply) {
            $collection->push(Reply::create($reply));
        }
        return new Replies($collection);
    }

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
