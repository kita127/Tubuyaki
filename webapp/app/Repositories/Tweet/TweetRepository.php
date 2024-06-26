<?php

namespace App\Repositories\Tweet;

use App\Entities\Identifiable\Id;
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
     * 
     * @param array<mixed, mixed> $where array<key, value>
     * @param null|int $offset 
     * @param null|int $limit 
     * @param null|array $orderBy 
     * @param string $description 
     * @return Collection<Tweet>
     */
    public function findAllBy(
        array $where,
        ?int $offset = null,
        ?int $limit = null,
        ?array $orderBy = null,
        string $description = 'asc'
    ): Collection;

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
     * 
     * @param Tweet $tweet  リツイートするツイート
     * @param User $user    リツイートするユーザー
     * @return Tweet 作成したリツイート
     */
    public function retweet(Tweet $tweet, User $user): Tweet;

    /**
     * リツイートを探す
     * @param User $user リツイートしたユーザー
     * @param Id $targetId リツイートしたつぶやきのID
     * @return Tweet|null
     */
    public function findRetweet(User $user, Id $targetId): ?Tweet;
}
