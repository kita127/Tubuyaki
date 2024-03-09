<?php

namespace App\Models;

use App\Entities\Entity;
use App\Entities\Follower as EF;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'followee_id',
    ];

    public function toEntity(): Entity
    {
        return new EF(
            id: $this->id,
            user_id: $this->user_id,
            followee_id: $this->followee_id,
        );
    }
}
