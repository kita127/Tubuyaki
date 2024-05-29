<?php

namespace App\Services\Tweet;

use App\Entities\Identifiable\Id;
use App\Entities\Identifiable\Identified;
use App\Repositories\Tweet\TweetRepository;
use App\Entities\Tweet as EntityTweet;
use App\Entities\TweetType;
use App\Repositories\User\UserRepository;
use App\Services\TubuyakiUser;
use Illuminate\Support\Collection;
use LogicException;

class TweetRetriever
{
    public function __construct(
        private readonly TweetRepository $tweetRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function getTweetById(Identified $id): Tweet
    {
        $target = $this->tweetRepository->find($id->value());
        return $this->createTweetFromEntity($target);
    }

    public function createTweetFromEntity(EntityTweet $entity): Tweet
    {
        $owner = $this->userRepository->find($entity->user_id);
        $owner = new TubuyakiUser($owner);
        $tweet = null;
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
     * @param TubuyakiUser $user リツイートしたユーザー
     * @param Id $targetId リツイートしたつぶやきのID
     * @return null|Tweet 
     * @throws LogicException 
     */
    public function findRetweetByTargetId(TubuyakiUser $user, Id $targetId): ?Tweet
    {
        $retweet = $this->tweetRepository->findRetweet($user->getEntity(), $targetId);
        if (is_null($retweet)) return null;
        return $this->createTweetFromEntity($retweet);
    }

    /**
     * 
     * @param string $key 
     * @param Collection<Id> $idList 
     * @return Collection<int, Tweet> 
     */
    public function findIn(string $key, Collection $idList): Collection
    {
        $array = $idList->map(fn (Id $id) => $id->value())->all();
        $entities = $this->tweetRepository->findIn([$key => $array]);
        $tweets = collect([]);
        foreach ($entities as $entity) {
            /** @var EntityTweet $entity */
            $t = $this->createTweetFromEntity($entity);
            $tweets->put($t->id()->value(), $t);
        }
        return $tweets;
    }
}
