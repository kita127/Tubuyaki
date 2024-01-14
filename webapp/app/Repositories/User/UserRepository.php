<?php

namespace App\Repositories\User;

use App\Entities\User;

interface UserRepository
{
    public function find(int $id): User;
}