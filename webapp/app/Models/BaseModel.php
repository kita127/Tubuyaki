<?php

namespace App\Models;

use App\Entities\Entity;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    abstract public function toEntity(): Entity;
}