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
            /** @var \App\Services\Tweet\Reply $reply */
            $owner = new User(
                $reply->owner->id->value(),
                $reply->owner->accountName(),
                $reply->owner->name(),
            );
            $tweet = new Tweet(
                $reply->tweet->id->value(),
                $reply->tweet->text,
                $reply->tweet->created_at,
                $reply->tweet->updated_at,
            );
            $collection->push(new Reply($owner, $tweet));
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
