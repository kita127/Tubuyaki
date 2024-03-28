<?php

namespace App\Repositories\Tweet;

use App\Models\BaseModel;
use App\Repositories\Eloquent\ElqCommonRepository;
use App\Repositories\Interface\Modifiable;
use App\Repositories\Tweet\TweetRepository;
use App\Entities\Tweet;
use App\Models\Tweet as ElqTweet;
use Illuminate\Support\Collection;
use LogicException;
use App\Entities\Entity;
use App\Models\Reply as ElqReply;
use App\Models\TweetDetail as ElqTweetDetail;
use App\Models\TweetType as ElqTweetType;

class ElqTweetRepository implements TweetRepository, Modifiable
{
    private readonly BaseModel $model;
    private readonly ElqCommonRepository $commonRepo;
    public function __construct()
    {
        $this->model = new ElqTweet();
        $this->commonRepo = new ElqCommonRepository($this->model);
    }

    /**
     * @param Tweet $tweet
     * @return Tweet
     */
    public function save(Tweet $tweet): Tweet
    {
        $result = $this->commonRepo->save($tweet, $this);
        if (!($result instanceof Tweet)) {
            throw new LogicException;
        }
        return $result;
    }

    /**
     * @param int $id
     * @return Tweet
     */
    public function find(int $id): Tweet
    {
        $result = $this->commonRepo->find($id);
        if (!($result instanceof Tweet)) {
            throw new LogicException;
        }
        return $result;
    }

    /**
     * @param array<string, mixed> $where
     * @return Collection<int, Tweet>    key:ID
     */
    public function findAllBy(array $where): Collection
    {
        return $this->commonRepo->findAllBy($where);
    }

    /**
     * @param Tweet $reply  返信つぶやき
     * @param Tweet $toTweet 返信対象のつぶやき
     */
    public function reply(Tweet $reply, Tweet $toTweet): void
    {
        $r = new ElqReply(['tweet_id' => $reply->id->value(), 'to_tweet_id' => $toTweet->id->value()]);
        $r->save();
    }

    /**
     * $tweetのすべての返信を取得する
     * デフォルトでは更新時間の降順で返される
     * @param Tweet $tweet
     * @param string $order 並べ替え対象のキー
     * @param string $by    asc, desc
     * @return Collection<Tweet>
     */
    public function findAllReplies(Tweet $tweet, string $order = null, string $by = null): Collection
    {
        /** @var Collection<ElqReply> $replyRelations */
        $replyRelations = ElqReply::where('to_tweet_id', $tweet->id->value())->get();
        $replyIdList = $replyRelations->pluck('tweet_id');

        $query = ElqTweet::whereIn('id', $replyIdList->all());
        if ($order) {
            if ($by !== 'asc' && $by !== 'desc') {
                throw new LogicException();
            }
            $query = $query->orderBy($order, $by);
        } else {
            // デフォルトは更新時間の降順
            $query = $query->orderBy('updated_at', 'desc');
        }
        /** @var Collection<ElqTweet> $replies */
        $replies = $query->get();
        $entities = $replies->map(fn (ElqTweet $t) => $t->toEntity());
        return $entities;
    }

    public function create(Entity $tweet): Entity
    {
        if (!($tweet instanceof Tweet)) {
            throw new LogicException();
        }
        $elqTweet = new ElqTweet([
            'user_id' => $tweet->user_id,
        ]);
        $elqTweet->save();
        $elqTweetDetail = new ElqTweetDetail();
        $type = ElqTweetType::where('value', '=', $tweet->type->value)->firstOrFail();
        $elqTweetDetail->tweet_type_id = $type->id;
        $elqTweetDetail->text = $tweet->text;
        $elqTweet->tweetDetail()->save($elqTweetDetail);
        return $elqTweet->toEntity();
    }

    public function update(Entity $tweet): Entity
    {
        if (!($tweet instanceof Tweet)) {
            throw new LogicException();
        }
        /** @var ElqTweet $elqTweet */
        $elqTweet = ElqTweet::findOrFail($tweet->id->value());
        $elqTweet->tweetDetail->text = $tweet->text;
        $elqTweet->tweetDetail->save();
        $elqTweet->created_at = $elqTweet->tweetDetail->created_at > $elqTweet->created_at
            ? $elqTweet->tweetDetail->created_at : $elqTweet->created_at;
        $elqTweet->updated_at = $elqTweet->tweetDetail->updated_at > $elqTweet->updated_at
            ? $elqTweet->tweetDetail->updated_at : $elqTweet->updated_at;
        $elqTweet->save();
        return $elqTweet->toEntity();
    }
}
