<?php

namespace App\Repositories\Tweet;

use App\Entities\Tweet;
use Illuminate\Support\Collection;

interface TweetRepository
{
    /**
     * @param Tweet $tweet
     * @return Tweet
     */
    public function save(Tweet $tweet): Tweet;

    /**
     * @param int $id
     * @return Tweet
     */
    public function find(int $id): Tweet;

    /**
     * @param array<string, mixed> $where
     * @return Collection<int, Tweet>    key:ID
     */
    public function findAllBy(array $where): Collection;

    /**
     * @param Tweet $reply  返信つぶやき
     * @param Tweet $toTweet 返信対象のつぶやき
     */
    public function reply(Tweet $reply, Tweet $toTweet): void;

    /**
     * $tweetのすべての返信を取得する
     * @param Tweet $tweet
     * @return Collection<Tweet>
     */
    public function findAllReplies(Tweet $tweet): Collection;
}
