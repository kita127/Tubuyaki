<?php

namespace App\Repositories\User;

use App\Entities\User;
use App\Models\User as ElqUser;
use App\Models\UserDetail as ElqUserDetail;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
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
            $elqUser->remember_token = $user->remember_token;
            $elqUser->save();

            $elqUserDetail = new ElqUserDetail();
            $elqUserDetail->account_name = $user->account_name;
            $elqUserDetail->name = $user->name;
            $elqUserDetail->email = $user->email;
            $elqUserDetail->password = Hash::make($user->password);

            $elqUser->userDetail()->save($elqUserDetail);
        } else {
            // update
            $elqUser = ElqUser::findOrFail($user->id);
            /** @var ElqUser $elqUser */
            $elqUser->userDetail->account_name = $user->account_name;
            $elqUser->userDetail->name = $user->name;
            $elqUser->userDetail->email = $user->email;
            $elqUser->userDetail->password = Hash::make($user->password);
            $elqUser->remember_token = $user->remember_token;
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
        $id = $query->first('users.id');
        if (!$id) {
            return null;
        }
        $elqUser = ElqUser::findOrFail($id)->first();
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
        $query = ElqUser::query()->join('user_details', 'users.id', '=', 'user_details.user_id');
        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }
        return $query;
    }

    private function createEntity(ElqUser $elqUser): User
    {
        return new User(
            id: $elqUser->id,
            account_name: $elqUser->userDetail->account_name,
            name: $elqUser->userDetail->name,
            email: $elqUser->userDetail->email,
            password: $elqUser->userDetail->password,
            remember_token: $elqUser->remember_token,
        );
    }
}