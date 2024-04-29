<?php

namespace App\Services\Tweet;

use App\Entities\Identifiable\Unidentified;
use App\Repositories\Tweet\TweetRepository;
use App\Services\TubuyakiUser;
use Illuminate\Support\Collection;
use App\Entities\Tweet as EntityTweet;
use App\Entities\TweetType;
use App\Repositories\User\UserRepository;

class TweetService
{
    public function __construct(
        private readonly TweetRepository $tweetRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * 
     * @param int $id 
     * @return Tweet 
     */
    public function getTweet(int $id): Tweet
    {
        $entity = $this->tweetRepository->find($id);
        $owner = $this->userRepository->find($entity->user_id);
        $owner = new TubuyakiUser($owner);
        return new Tweet($owner, $entity);
    }

    /**
     * @param TubuyakiUser $user
     * @return array{tweets: Collection<EntityTweet>, next: int|null}
     */
    public function getTweets(TubuyakiUser $user, int $index, int $count): array
    {
        $tweets = $this->tweetRepository->findAllBy(
            ['user_id' => $user->id->value()],
            $index,
            $count + 1,
            ['updated_at', 'id'],
            'desc',
        );
        if ($tweets->count() > $count) {
            // 最後は不要なので捨てる
            $tweets->pop();
            $next = $index + $count;
        } else {
            $next = null;
        }
        return ['tweets' => $tweets, 'next' => $next];
    }

    /**
     * @param TubuyakiUser $user
     * @param string $text
     * @return EntityTweet
     */
    public function post(TubuyakiUser $user, string $text, TweetType $tweetType): EntityTweet
    {
        $tweet = new EntityTweet(new Unidentified(), $user->id->value(), $tweetType, $text);
        return $this->tweetRepository->save($tweet);
    }

    /**
     * @param EntityTweet $tweet
     * @return Collection<Reply>
     */
    public function getReplies(EntityTweet $tweet): Collection
    {
        /** @var Collection<EntityTweet> $replies */
        $replies = $this->tweetRepository->findAllReplies($tweet);
        $owners = $this->userRepository->findIn(['id' => $replies->pluck('user_id')->all()]);
        $result = collect([]);
        /** @var EntityTweet $tweet */
        foreach ($replies as $tweet) {
            $owner = $owners->get($tweet->user_id);
            $reply = new Reply(new TubuyakiUser($owner), $tweet);
            $result->push($reply);
        }
        return $result;
    }

    public function reply(EntityTweet $tweet, TubuyakiUser $user, string $text): void
    {
        $reply = $this->post($user, $text, TweetType::Reply);
        $this->tweetRepository->reply($reply, $tweet);
    }

    public function retweet(EntityTweet $tweet, TubuyakiUser $user): void
    {
        $this->tweetRepository->retweet($tweet, $user->getEntity());
    }
}
