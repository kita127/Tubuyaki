<?php

namespace App\Services\Tweet;

use App\Entities\Identifiable\Id;
use App\Entities\Tweet as EntitiesTweet;
use App\Services\TubuyakiUser;
use App\Http\Responses\Tweet\Tweet as Response;

class Reply implements Tweet
{
    public function __construct(
        public readonly TubuyakiUser $owner,
        public readonly EntitiesTweet $tweet,
        public readonly Tweet $target,
    ) {
    }

    public function id(): Id
    {
        return $this->tweet->id;
    }

    public function createResponse(): Response
    {
        $targetId = $this->target->id();
        return new Response(
            $this->tweet->id->value(),
            \App\Http\Responses\User::create($this->owner),
            $this->tweet->type,
            $this->tweet->text,
            $targetId->isIdentified() ? $targetId->value() : null,
            $this->tweet->created_at,
            $this->tweet->updated_at,
        );
    }
}
