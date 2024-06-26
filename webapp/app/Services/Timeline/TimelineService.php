<?php

namespace App\Services\Timeline;

use App\Repositories\Tweet\TweetRepository;
use App\Repositories\User\UserRepository;
use App\Services\TubuyakiUser;
use App\Services\Tweet\Tweet;
use App\Services\Tweet\TweetRetriever;
use Illuminate\Support\Collection;

class TimelineService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TweetRepository $tweetRepository,
        private readonly TweetRetriever $tweetRetriever,
    ) {
    }

    public function getTimeline(TubuyakiUser $user, int $index, int $count): TimelineContents
    {
        /** @var array<int, \App\Entities\User> $targetUserMap */
        $targetUserMap = [$user->id->value() => $user->getEntity()];
        $followees = $this->userRepository->findFollowees($user->getEntity());
        /** @var \App\Entities\User $followee */
        foreach ($followees as $followee) {
            $targetUserMap[$followee->id->value()] = $followee;
        }
        $tweetEntities = $this->tweetRepository->findIn(
            ['user_id' => array_keys($targetUserMap)],
            $index,
            $count + 1,
            ['updated_at', 'id'],
            'desc',
        );
        if ($tweetEntities->count() > $count) {
            $tweetEntities->pop();  // 最後は余分なので削除
            $next = $index + $count;
        } else {
            $next = null;
        }
        /** @var Collection<Tweet> $tweets*/
        $tweets = collect([]);
        /** @var \App\Entities\Tweet $entity */
        foreach ($tweetEntities as $entity) {
            $tweet = $this->tweetRetriever->createTweetFromEntity($entity);
            $tweets->push($tweet);
        }
        return new TimelineContents($tweets, $next);
    }
}
