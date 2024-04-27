<?php

namespace App\Services\Timeline;

use App\Services\Tweet\Tweet;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use LogicException;

class TimelineContents
{
    /**
     * @param Collection<Tweet> $myTweets
     * @param Collection<Tweet> $followeeTweets
     */
    public function __construct(
        public readonly Collection $myTweets,
        public readonly Collection $followeeTweets,
    ) {
    }

    /**
     * 自分とフォロイーのツイートをupdated_atの降順でまとめる
     * @return Collection<Tweet>
     */
    public function mergeTweets(): Collection
    {
        $tweets = collect([]);

        $myTweets = $this->myTweets;
        $followeeTweets = $this->followeeTweets;

        while ($res = $this->getNewer($myTweets, $followeeTweets)) {
            $tweets->push($res);
        }
        return $tweets;
    }

    /**
     * @param Collection<Tweet> $myTweets
     * @param Collection<Tweet> $followeeTweets
     */
    private function getNewer(Collection &$myTweets, Collection &$followeeTweets): ?Tweet
    {
        if ($myTweets->count() <= 0 && $followeeTweets->count() <= 0) {
            return null;
        }
        if ($myTweets->count() <= 0) {
            return $followeeTweets->pop();
        } elseif ($followeeTweets->count() <= 0) {
            return $myTweets->pop();
        }
        /** @var ?Tweet $x */
        $mine = $myTweets->first();
        /** @var ?Tweet $y */
        $others = $followeeTweets->first();
        $myDate = $mine->entity->updated_at ? new Carbon($mine->entity->updated_at) : throw new LogicException();
        $othersDate = $others->entity->updated_at ? new Carbon($others->entity->updated_at) : throw new LogicException();

        if ($othersDate->isAfter($myDate)) {
            return $followeeTweets->pop();
        } else {
            // 同じ時も自分
            return $myTweets->pop();
        }
    }
}
