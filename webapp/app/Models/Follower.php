<?php

namespace App\Models;

use App\Entities\Entity;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Entities\Identifiable\Identified;

class Follower extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'followee_id',
    ];

    public function toEntity(): Entity
    {
        return new \App\Entities\Follower(
            id: new Identified($this->id),
            user_id: $this->user_id,
            followee_id: $this->followee_id,
        );
    }
}
