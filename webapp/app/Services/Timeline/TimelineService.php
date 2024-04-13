<?php

namespace App\Services\Timeline;

use App\Repositories\Tweet\TweetRepository;
use App\Repositories\User\UserRepository;
use App\Services\TubuyakiUser;

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
        $myTweets = $this->tweetRepository->findAllBy(['user_id' => $user->id->value()]);
        // 自分のフォロイーのつぶやきを取得する
        $followees = $this->userRepository->findFollowees($user->getEntity());
        $followeeTweets = collect([]);
        foreach ($followees as $followee) {
            $tweets = $this->tweetRepository->findAllBy(['user_id' => $followee->id->value()]);
            $followeeTweets->push($tweets);
        }
        return new TimelineContents($myTweets, $followeeTweets);
    }
}
