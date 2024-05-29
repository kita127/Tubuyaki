<?php

namespace Tests\Lib;

use App\Entities\Identifiable\Unidentified;
use App\Entities\Tweet;
use App\Entities\TweetType;
use App\Repositories\Tweet\TweetRepository;
use App\Repositories\User\UserRepository;
use App\Services\TubuyakiUser;
use Illuminate\Support\Collection;

class TweetCreator
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TweetRepository $tweetRepository,
    ) {
    }

    public function create(TubuyakiUser $user, string $content, TweetType $type): Tweet
    {
        $tweet = new Tweet(new Unidentified(), $user->id->value(), $type, $content, new Unidentified());
        return $this->tweetRepository->save($tweet);
    }

    /**
     * @return Collection<Tweet>
     */
    public function createTweets(TubuyakiUser $user, int $count, TweetType $type): Collection
    {
        $tweets = collect([]);
        for ($i = 0; $i < $count; $i++) {
            $text = fake()->realText(140);
            $t = $this->create($user, $text, $type);
            $tweets->push($t);
        }
        return $tweets;
    }
}
