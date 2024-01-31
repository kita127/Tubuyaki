<?php

namespace App\Repositories\User;

use App\Entities\User;
use App\Models\User as ElqUser;

class ElqUserRepository implements UserRepository
{
    public function find(int $id): User
    {
        $elqUser = ElqUser::findOrFail($id);
        return $this->createEntity($elqUser);
    }

    public function save(User $user): bool
    {
        if (!$user->id) {
            // create
            $elqUser = new ElqUser();
        } else {
            // update
            $elqUser = ElqUser::findOrFail($user->id);
        }
        $elqUser->account_name = $user->account_name;
        $elqUser->name = $user->name;
        $elqUser->email = $user->email;
        $elqUser->password = $user->password;
        $elqUser->remember_token = $user->remember_token;
        return $elqUser->save();
    }

    /**
     * @param array<string, mixed> $where
     * @return User | null
     */
    public function findOneBy(array $where): ?User
    {
        $query = (new ElqUser())->newQuery();
        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }
        $elqUser = $query->first();
        if (!$elqUser) {
            return null;
        }
        return $this->createEntity($elqUser);
    }

    private function createEntity(ElqUser $elqUser): User
    {
        return new User(
            id: $elqUser->id,
            account_name: $elqUser->account_name,
            name: $elqUser->name,
            email: $elqUser->email,
            password: $elqUser->password,
            remember_token: $elqUser->remember_token,
        );
    }
}