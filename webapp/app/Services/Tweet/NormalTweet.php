<?php

namespace App\Services\Tweet;

use App\Entities\Identifiable\Id;
use App\Entities\Tweet as EntitiesTweet;
use App\Http\Responses\Tweet\Tweet as Response;
use App\Services\TubuyakiUser;

class NormalTweet implements Tweet
{
    public function __construct(
        public readonly TubuyakiUser $owner,
        public readonly EntitiesTweet $entity,
    ) {
    }

    public function id(): Id
    {
        return $this->entity->id;
    }

    public function createResponse(): Response
    {
        return new Response(
            $this->entity->id->value(),
            \App\Http\Responses\User::create($this->owner),
            $this->entity->type,
            $this->entity->text,
            null,
            $this->entity->created_at,
            $this->entity->updated_at,
        );
    }

    public function isOwner(TubuyakiUser $user): bool
    {
        return $this->owner->same($user);
    }

    public function entity(): EntitiesTweet
    {
        return $this->entity;
    }
}
