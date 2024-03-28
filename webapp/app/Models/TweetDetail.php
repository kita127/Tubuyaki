<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TweetDetail extends Model
{
    use HasFactory;

    public function tweet(): BelongsTo
    {
        return $this->belongsTo(Tweet::class);
    }

    public function tweetType(): BelongsTo
    {
        return $this->belongsTo(TweetType::class);
    }
}
