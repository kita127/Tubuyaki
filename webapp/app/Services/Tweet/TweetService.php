<?php

namespace App\Services\Tweet;

use App\Repositories\Tweet\TweetRepository;
use App\Services\TubuyakiUser;
use Illuminate\Support\Collection;
use App\Entities\Tweet;

class TweetService
{
    public function __construct(
        private readonly TweetRepository $tweetRepository,
    ) {
    }
    /**
     * @param TubuyakiUser $user
     * @return Collection<array>
     */
    public function getTweets(TubuyakiUser $user): Collection
    {
        $tweets = $this->tweetRepository->findAllBy(['user_id' => $user->id->value()]);
        return $tweets->map(fn(Tweet $tweet) => [
            'text' => $tweet->text,
            'created_at' => $tweet->created_at,
            'updated_at' => $tweet->updated_at,
        ]);
    }
}