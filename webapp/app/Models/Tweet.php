<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Entities\Entity;
use App\Entities\Tweet as ETweet;
use App\Entities\Identifiable\Identified;

class Tweet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'text',
    ];

    public function toEntity(): Entity
    {
        return new ETweet(
            id: new Identified($this->id),
            user_id: $this->user_id,
            text: $this->text,
            created_at: $this->created_at,
            updated_at: $this->updated_at,
        );
    }
}
