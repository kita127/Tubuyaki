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
     * @param Tweet $tweet
     * @return Collection<Tweet>
     */
    public function findAllReplies(Tweet $tweet): Collection
    {
        /** @var Collection<ElqReply> $replyRelations */
        $replyRelations = ElqReply::where('to_tweet_id', $tweet->id->value())->get();
        $replyIdList = $replyRelations->pluck('tweet_id');
        /** @var Collection<ElqTweet> $replies */
        $replies = ElqTweet::whereIn('id', $replyIdList->all())->get();
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
            'text' => $tweet->text,
        ]);
        $elqTweet->save();
        return $elqTweet->toEntity();
    }

    public function update(Entity $tweet): Entity
    {
        if (!($tweet instanceof Tweet)) {
            throw new LogicException();
        }
        /** @var ElqTweet $elqTweet */
        $elqTweet = ElqTweet::findOrFail($tweet->id->value());
        $elqTweet->user_id = $tweet->user_id;
        $elqTweet->text = $tweet->text;
        $elqTweet->save();
        return $elqTweet->toEntity();
    }
}
