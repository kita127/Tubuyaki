<?php

namespace App\Services\Tweet;

use App\Entities\Identifiable\Unidentified;
use App\Repositories\Tweet\TweetRepository;
use App\Services\TubuyakiUser;
use Illuminate\Support\Collection;
use App\Entities\Tweet;
use App\Repositories\User\UserRepository;

class TweetService
{
    public function __construct(
        private readonly TweetRepository $tweetRepository,
        private readonly UserRepository $userRepository,
    ) {
    }
    /**
     * @param TubuyakiUser $user
     * @return Collection<array>
     */
    public function getTweets(TubuyakiUser $user): Collection
    {
        $tweets = $this->tweetRepository->findAllBy(['user_id' => $user->id->value()]);
        return $tweets->map(fn (Tweet $tweet) => [
            'text' => $tweet->text,
            'created_at' => $tweet->created_at,
            'updated_at' => $tweet->updated_at,
        ]);
    }

    /**
     * @param TubuyakiUser $user
     * @param string $text
     * @return Tweet
     */
    public function post(TubuyakiUser $user, string $text): Tweet
    {
        $tweet = new Tweet(new Unidentified(), $user->id->value(), $text);
        return $this->tweetRepository->save($tweet);
    }

    /**
     * @param Tweet $tweet
     * @return Collection<Reply>
     */
    public function getReplies(Tweet $tweet): Collection
    {
        /** @var Collection<Tweet> $replies */
        $replies = $this->tweetRepository->findAllReplies($tweet);
        $owners = $this->userRepository->findIn(['id' => $replies->pluck('user_id')->all()]);
        $result = collect([]);
        /** @var Tweet $tweet */
        foreach ($replies as $tweet) {
            $owner = $owners->get($tweet->user_id);
            $reply = new Reply(new TubuyakiUser($owner), $tweet);
            $result->push($reply);
        }
        return $result;
    }

    public function reply(Tweet $tweet, TubuyakiUser $user, string $text): void
    {
        $reply = $this->post($user, $text);
        $this->tweetRepository->reply($reply, $tweet);
    }
}
