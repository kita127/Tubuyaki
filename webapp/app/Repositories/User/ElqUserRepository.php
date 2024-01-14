<?php

namespace App\Repositories\User;

use App\Entities\User;
use App\Models\User as ElqUser;

class ElqUserRepository implements UserRepository
{
    public function find(int $id): User
    {
        $elqUser = ElqUser::findOrFail($id);
        return new User(
            id: $elqUser->id,
            name: $elqUser->name,
            email: $elqUser->email,
            password: $elqUser->password,
        );
    }
}