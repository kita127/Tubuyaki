<?php

namespace App\Repositories\Tweet;

use App\Repositories\Eloquent\ElqCommonRepository;
use App\Repositories\Interface\Modifiable;
use App\Repositories\Tweet\TweetRepository;
use App\Entities\Tweet;
use App\Models\Tweet as ElqTweet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use LogicException;
use App\Entities\Entity;

class ElqTweetRepository implements TweetRepository, Modifiable
{
    private readonly Model $model;
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
     * @param array<string, mixed> $where
     * @return Collection<int, Tweet>    key:ID
     */
    public function findAllBy(array $where): Collection
    {
        return $this->commonRepo->findAllBy($where);
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