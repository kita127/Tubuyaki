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
        private readonly TweetRetriever $tweetRetriever,
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
        return $this->tweetRetriever->createTweetFromEntity($entity);
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
            // TODO: Eargerローディングする
            $owner = $owners->get($tweet->user_id);
            $target = $this->tweetRetriever->getTweetById($tweet->target_id);
            $reply = new Reply(new TubuyakiUser($owner), $tweet, $target);
            $result->push($reply);
        }
        return $result;
    }

    public function reply(EntityTweet $targetTweet, TubuyakiUser $user, string $text): void
    {
        $reply = $this->post($user, $text, TweetType::Reply, $targetTweet->id);
    }

    /**
     * 
     * @param int $tweetId リツイートするつぶやきのID
     * @param TubuyakiUser $user 自分
     * @return void 
     */
    public function retweet(int $tweetId, TubuyakiUser $user): void
    {
        $tweet = $this->tweetRetriever->getTweetById(new Identified($tweetId));
        if ($tweet->isOwner($user)) {
            throw new LogicException('自分のつぶやきにはリツイートできません');
        }
        $exists = $this->tweetRetriever->findRetweetByTargetId($user, $tweet->id());
        if ($exists) {
            throw new LogicException('同じつぶやきに再度リツイートしています');
        }
        $this->tweetRepository->retweet($tweet->entity(), $user->getEntity());
    }
}
