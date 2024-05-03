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
use App\Entities\Identifiable\Unidentified;
use App\Entities\TweetType;
use App\Models\Reply as ElqReply;
use App\Models\TweetDetail as ElqTweetDetail;
use App\Models\TweetType as ElqTweetType;
use App\Models\Retweet as ElqRetweet;
use App\Models\User as ElqUser;
use App\Entities\User;
use Illuminate\Contracts\Database\Eloquent\Builder;

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
     * 
     * @param array $where 
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
        if (is_array($where)) {
            $query = $this->commonRepo->createWhereQuery($where);
        } elseif ($where instanceof Builder) {
            $query = $where;
        } else {
            throw new LogicException();
        }
        if ($offset) {
            $query->skip($offset);
        }
        if ($limit) {
            $query->take($limit);
        }
        if ($orderBy) {
            if ($description !== 'asc' && $description !== 'desc') {
                throw new LogicException('description is expected asc or desc.');
            }
            foreach ($orderBy as $col) {
                $query->orderBy($col, $description);
            }
        }
        /** @var Collection<BaseModel> $models */
        $models = $query->get();
        return $models->map(fn (BaseModel $f) => $f->toEntity())->keyBy('id');
    }

    /**
     * @param array<string, array> $whereIn
     * @param ?int $offset
     * @param ?int $limit
     * @param ?array<string> $orderBy
     * @param string $direction
     * @return Collection<int, Tweet>    key:ID
     */
    public function findIn(
        array $whereIn,
        ?int $offset = null,
        ?int $limit = null,
        ?array $orderBy = null,
        string $direction = 'asc'
    ): Collection {
        $keys = array_keys($whereIn);
        if (count($keys) !== 1) {
            throw new LogicException();
        }
        $values = array_values($whereIn);
        if (count($values) !== 1) {
            throw new LogicException();
        }
        if (!is_array($values[0])) {
            throw new LogicException();
        }
        $query = $this->model->query()->whereIn($keys[0], $values[0]);

        if ($offset) {
            $query->skip($offset);
        }
        if ($limit) {
            $query->take($limit);
        }
        if ($orderBy) {
            if ($direction !== 'asc' && $direction !== 'desc') {
                new LogicException('Direction must be asc or desc.');
            }
            foreach ($orderBy as $col) {
                $query->orderBy($col, $direction);
            }
        }
        /** @var Collection<BaseModel> $models */
        $models = $query->get();
        $result = collect([]);
        foreach ($models as $model) {
            $result->put($model->id, $model->toEntity());
        }
        return $result;
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

    /**
     * $tweetをリツイートしたユーザーの一覧を取得する
     * デフォルトではリツイートした時間の降順で取得する
     * @param Tweet $tweet
     * @return Collection<User>
     */
    public function findRetweetUsers(Tweet $tweet): Collection
    {
        $retweetRelations = ElqRetweet::where('target_id', '=', $tweet->id->value())
            ->orderByDesc('created_at')->get();
        $retweetIdList = $retweetRelations->pluck('tweet_id');
        $retweets = ElqTweet::whereIn('id', $retweetIdList->toArray())->get();
        $users = ElqUser::whereIn('id', $retweets->pluck('user_id')->all())->get();
        return $users->map(fn (ElqUser $u) => $u->toEntity());
    }

    /**
     * 
     * @param Tweet $tweet  リツイートするツイート
     * @param User $user    リツイートするユーザー
     * @return Tweet 作成したリツイート
     */
    public function retweet(Tweet $tweet, User $user): Tweet
    {
        $myTweet = new Tweet(new Unidentified(), $user->id->value(), TweetType::Retweet, '', $tweet->id);
        /** @var Tweet $myTweet */
        return $this->create($myTweet);
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
        $elqTweet->refresh();
        $newId = $elqTweet->id;

        if ($tweet->type === TweetType::Retweet) {
            $relation = new ElqRetweet([
                'tweet_id' => $newId,
                'target_id' => $tweet->target_id->value()
            ]);
            $relation->save();
        } elseif ($tweet->type === TweetType::Reply) {
            $relation = new ElqReply([
                'tweet_id' => $newId,
                'to_tweet_id' => $tweet->target_id->value(),
            ]);
        }

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
