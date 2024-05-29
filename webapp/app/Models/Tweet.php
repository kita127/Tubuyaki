<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Entities\Entity;
use App\Entities\Identifiable\Identified;
use App\Entities\Identifiable\Unidentified;
use App\Entities\TweetType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LogicException;

class Tweet extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'text',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tweetDetail(): HasOne
    {
        return $this->hasOne(TweetDetail::class);
    }

    public function retweet(): HasOne
    {
        return $this->hasOne(Retweet::class);
    }

    public function reply(): HasOne
    {
        return $this->hasOne((Reply::class));
    }

    public function toEntity(): Entity
    {
        /** @var TweetDetail $tweetDetail */
        $tweetDetail = $this->tweetDetail;
        /** @var Retweet $retweet */
        $retweet = $this->retweet;
        /** @var Reply $reply */
        $reply = $this->reply;

        if ($retweet && $tweetDetail->tweetType->value !== 'retweet') {
            throw new LogicException();
        }
        if ($reply && $tweetDetail->tweetType->value !== 'reply') {
            throw new LogicException();
        }

        $targetId = new Unidentified();
        if ($retweet) {
            $targetId = new Identified($retweet->target_id);
        }
        if ($reply) {
            $targetId = new Identified($reply->to_tweet_id);
        }

        return new \App\Entities\Tweet(
            id: new Identified($this->id),
            user_id: $this->user_id,
            type: TweetType::tryFrom($this->tweetDetail->tweetType->value),
            text: $this->tweetDetail->text,
            target_id: $targetId,
            created_at: $this->created_at,
            updated_at: $this->updated_at,
        );
    }
}
