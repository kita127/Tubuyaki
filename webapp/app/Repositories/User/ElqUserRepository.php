<?php

namespace App\Repositories\User;

use App\Entities\User;
use App\Models\User as ElqUser;
use Illuminate\Contracts\Database\Eloquent\Builder;
use RuntimeException;
use Illuminate\Support\Collection;

class ElqUserRepository implements UserRepository
{
    public function find(int $id): User
    {
        $elqUser = ElqUser::findOrFail($id);
        return $this->createEntity($elqUser);
    }

    public function save(User $user): User
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
        if (!$elqUser->save()) {
            throw new RuntimeException();
        }
        return $this->createEntity($elqUser);
    }

    /**
     * @param array<string, mixed> $where
     * @return User | null
     */
    public function findOneBy(array $where): ?User
    {
        $query = $this->createWhereQuery($where);
        $elqUser = $query->first();
        if (!$elqUser) {
            return null;
        }
        return $this->createEntity($elqUser);
    }

    /**
     * @param array<string, mixed> $where
     * @return Collection<int, User>
     */
    public function findAllBy(array $where): Collection
    {
        $query = $this->createWhereQuery($where);
        $elqUsers = $query->get();
        $users = collect([]);
        foreach ($elqUsers as $eu) {
            /** @var ElqUser $eu */
            $users->put($eu->id, $eu);
        }
        return $users;
    }

    /**
     * @param array<string, mixed> $where
     * @return Builder
     */
    private function createWhereQuery(array $where): Builder
    {
        // TODO: これwhereなかった時どんなクエリになるか確認する
        $query = (new ElqUser())->newQuery();
        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }
        return $query;
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