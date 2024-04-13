<?php

namespace App\Services\Timeline;

use App\Repositories\Tweet\TweetRepository;
use App\Repositories\User\UserRepository;
use App\Services\TubuyakiUser;
use App\Services\Tweet\Tweet;

class TimelineService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TweetRepository $tweetRepository,
    ) {
    }

    public function getTimeline(TubuyakiUser $user): TimelineContents
    {
        // 自分のつぶやきを取得する
        $entities = $this->tweetRepository->findAllBy(['user_id' => $user->id->value()]);
        $myTweets = collect([]);
        foreach ($entities as $entity) {
            $myTweets->push(new Tweet($user, $entity));
        }

        // 自分のフォロイーのつぶやきを取得する
        $followees = $this->userRepository->findFollowees($user->getEntity());
        $followeeTweets = collect([]);
        foreach ($followees as $followee) {
            $entities = $this->tweetRepository->findAllBy(['user_id' => $followee->id->value()]);
            foreach ($entities as $entity) {
                $user = new TubuyakiUser($followee);
                $followeeTweets->push(new Tweet($user, $entity));
            }
        }
        return new TimelineContents($myTweets, $followeeTweets);
    }
}
