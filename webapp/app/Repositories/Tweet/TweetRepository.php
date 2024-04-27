<?php

namespace App\Repositories\Tweet;

use App\Entities\Tweet;
use App\Entities\User;
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
     * @param ?int $offset
     * @param ?int $limit
     * @return Collection<int, Tweet>    key:ID
     */
    public function findAllBy(array $where, ?int $offset = null, ?int $limit = null): Collection;

    /**
     * @param array<string, array> $whereIn
     * @param ?int $offset
     * @param ?int $limit
     * @param ?array<string> $orderBy
     * @param string $direction
     * @return Collection<int, Tweet>    key:ID
     */
    public function findIn(array $whereIn, ?int $offset = null, ?int $limit = null, ?array $orderBy = null, string $direction = 'asc'): Collection;

    /**
     * @param Tweet $reply  返信つぶやき
     * @param Tweet $toTweet 返信対象のつぶやき
     */
    public function reply(Tweet $reply, Tweet $toTweet): void;

    /**
     * $tweetのすべての返信を取得する
     * デフォルトでは更新時間の降順で返される
     * @param Tweet $tweet
     * @param string $order 並べ替え対象のキー
     * @param string $by    asc, desc
     * @return Collection<Tweet>
     */
    public function findAllReplies(Tweet $tweet, ?string $order = null, ?string $by = null): Collection;

    /**
     * $tweetをリツイートしたユーザーの一覧を取得する
     * デフォルトではリツイートした時間の降順で取得する
     * @param Tweet $tweet
     * @return Collection<User>
     */
    public function findRetweetUsers(Tweet $tweet): Collection;

    /**
     * @param Tweet $tweet  リツイートするツイート
     * @param User $user    リツイートするユーザー
     */
    public function retweet(Tweet $tweet, User $user): void;
}
