<?php

namespace App\Services\Tweet;

use App\Entities\Identifiable\Identified;
use App\Entities\Identifiable\Unidentified;
use App\Repositories\Tweet\TweetRepository;
use App\Services\TubuyakiUser;
use Illuminate\Support\Collection;
use App\Entities\Tweet as EntityTweet;
use App\Entities\TweetType;
use App\Repositories\User\UserRepository;
use LogicException;

class TweetService
{
    public function __construct(
        private readonly TweetRepository $tweetRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    private function createTweetFromEntity(EntityTweet $entity): Tweet
    {
        $owner = $this->userRepository->find($entity->user_id);
        $owner = new TubuyakiUser($owner);
        $tweet = null;
        // TODO: ポリモフィズムで
        switch ($entity->type) {
            case TweetType::Normal:
                $tweet = new NormalTweet($owner, $entity);
                break;
            case TweetType::Reply:
                if (!$entity->target_id->isIdentified()) throw new LogicException();
                $target = $this->getTweetById($entity->target_id);
                $tweet = new Reply($owner, $entity, $target);
                break;
            case TweetType::Retweet:
                $targetId = $entity->target_id;
                if (!$targetId->isIdentified()) throw new LogicException();
                $target = $this->getTweetById($entity->target_id);
                $tweet = new Retweet($owner, $entity, $target);
                break;
            default:
                throw new LogicException();
        }
        return $tweet;
    }

    /**
     * 
     * @param int $id 
     * @return Tweet 
     */
    public function getTweet(int $id): Tweet
    {
        $entity = $this->tweetRepository->find($id);
        return $this->createTweetFromEntity($entity);
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
    public function post(TubuyakiUser $user, string $text, TweetType $tweetType, ?Identified $targetId = null): EntityTweet
    {
        $targetId = $targetId ?? new Unidentified();
        $tweet = new EntityTweet(new Unidentified(), $user->id->value(), $tweetType, $text, $targetId);
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

    public function reply(EntityTweet $targetTweet, TubuyakiUser $user, string $text): void
    {
        $reply = $this->post($user, $text, TweetType::Reply, $targetTweet->id);
        $this->tweetRepository->reply($reply, $targetTweet);
    }

    public function retweet(EntityTweet $tweet, TubuyakiUser $user): void
    {
        $this->tweetRepository->retweet($tweet, $user->getEntity());
    }

    public function getTweetById(Identified $id): Tweet
    {
        $target = $this->tweetRepository->find($id->value());
        return $this->createTweetFromEntity($target);
    }
}
