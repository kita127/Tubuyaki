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

    public function getTimeline(TubuyakiUser $user, ?int $index, ?int $count): TimelineContents
    {
        // TODO: ソートはDBでやるようにする
        // 自分のつぶやきを取得する
        $entities = $this->tweetRepository->findAllBy(['user_id' => $user->id->value()]);
        $entities = $entities->sortByDesc('updated_at');
        $myTweets = collect([]);
        foreach ($entities as $entity) {
            $myTweets->push(new Tweet($user, $entity));
        }

        // TODO: ソートはDBでやるようにする
        // 自分のフォロイーのつぶやきを取得する
        $followees = $this->userRepository->findFollowees($user->getEntity());
        $followeeTweets = collect([]);
        $entities = collect([]);
        foreach ($followees as $followee) {
            $es = $this->tweetRepository->findAllBy(['user_id' => $followee->id->value()]);
            $entities = $entities->merge($es);
        }
        $entities = $entities->sortByDesc('updated_at');
        foreach ($entities as $entity) {
            $user = new TubuyakiUser($followee);
            $followeeTweets->push(new Tweet($user, $entity));
        }

        return new TimelineContents($myTweets, $followeeTweets);
    }
}
