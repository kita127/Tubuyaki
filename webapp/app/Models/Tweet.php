<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Entities\Entity;
use App\Entities\Identifiable\Identified;

class Tweet extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'text',
        'created_at',
        'updated_at',
    ];

    public function tweetDetail()
    {
        return $this->hasOne(TweetDetail::class);
    }

    public function toEntity(): Entity
    {
        return new \App\Entities\Tweet(
            id: new Identified($this->id),
            user_id: $this->user_id,
            text: $this->tweetDetail->text,
            created_at: $this->created_at,
            updated_at: $this->updated_at,
        );
    }
}
