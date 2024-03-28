<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TweetType extends Model
{
    use HasFactory;

    public function tweetDetails(): HasMany
    {
        return $this->hasMany(TweetDetail::class);
    }
}
