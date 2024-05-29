<?php

namespace App\Repositories\Tweet;

use App\Entities\Identifiable\Id;
use App\Entities\Identifiable\Identified;
use App\Entities\Tweet;
use App\Entities\TweetType;
use App\Entities\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use LogicException;
use RuntimeException;

class MockTweetRepository implements TweetRepository
{
    // TODO: staticにする
    /**
     * @var array<int, Tweet>
     */
    private array $dummyRecords = [];
    private int $autoIncrement = 1;

    /**
     * @param Tweet $tweet
     * @return Tweet
     */
    public function save(Tweet $tweet): Tweet
    {
        if ($tweet->id->isIdentified()) {
            $id = $tweet->id;
        } else {
            $id = new Identified($this->autoIncrement);
            $this->autoIncrement++;
        }
        $newTweet = new Tweet(
            id: $id,
            user_id: $tweet->user_id,
            type: $tweet->type,
            text: $tweet->text,
            target_id: $tweet->target_id,
            created_at: $tweet->created_at,
            updated_at: $tweet->updated_at,
        );
        $this->dummyRecords[$id->value()] = $newTweet;
        return $newTweet;
    }

    /**
     * @param int $id
     * @return Tweet
     */
    public function find(int $id): Tweet
    {
        return $this->dummyRecords[$id] ?? throw new LogicException('つぶやきがありません');
    }

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
    ): Collection {
        throw new RuntimeException('unimplemented');
    }

    /**
     * @param array<string, array> $whereIn
     * @param ?int $offset
     * @param ?int $limit
     * @param ?array<string> $orderBy
     * @param string $direction
     * @return Collection<int, Tweet>    key:ID
     */
    public function findIn(array $whereIn, ?int $offset = null, ?int $limit = null, ?array $orderBy = null, string $direction = 'asc'): Collection
    {
        throw new RuntimeException('unimplemented');
    }

    /**
     * $tweetのすべての返信を取得する
     * デフォルトでは更新時間の降順で返される
     * @param Tweet $tweet
     * @param string $order 並べ替え対象のキー
     * @param string $by    asc, desc
     * @return Collection<Tweet>
     */
    public function findAllReplies(Tweet $tweet, ?string $order = null, ?string $by = null): Collection
    {
        throw new RuntimeException('unimplemented');
    }

    /**
     * $tweetをリツイートしたユーザーの一覧を取得する
     * デフォルトではリツイートした時間の降順で取得する
     * @param Tweet $tweet
     * @return Collection<User>
     */
    public function findRetweetUsers(Tweet $tweet): Collection
    {
        throw new RuntimeException('unimplemented');
    }

    /**
     * 
     * @param Tweet $tweet  リツイートするツイート
     * @param User $user    リツイートするユーザー
     * @return Tweet 作成したリツイート
     */
    public function retweet(Tweet $tweet, User $user): Tweet
    {
        $newTweet = new Tweet(
            id: new Identified($this->autoIncrement),
            user_id: $user->id->value(),
            type: TweetType::Retweet,
            text: '',
            target_id: $tweet->id,
            created_at: Carbon::now()->format('yyyy-mm-dd hh:ii:ss'),
            updated_at: Carbon::now()->format('yyyy-mm-dd hh:ii:ss'),
        );
        $this->dummyRecords[$newTweet->id->value()] = $newTweet;
        $this->autoIncrement++;
        return $newTweet;
    }

    /**
     * リツイートを探す
     * @param User $user リツイートしたユーザー
     * @param Id $targetId リツイートしたつぶやきのID
     * @return Tweet|null
     */
    public function findRetweet(User $user, Id $targetId): ?Tweet
    {
        foreach ($this->dummyRecords as $tweet) {
            /** @var Tweet $tweet */
            if (
                $tweet->user_id === $user->id->value()
                && $tweet->type == TweetType::Retweet
                && $tweet->target_id->equal($targetId)
            ) {
                return $tweet;
            }
        }
        return null;
    }
}
