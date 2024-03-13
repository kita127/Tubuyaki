<?php

namespace App\Repositories\User;

use App\Entities\Entity;
use App\Entities\User;
use App\Models\User as ElqUser;
use App\Models\UserDetail as ElqUserDetail;
use App\Repositories\Interface\Modifiable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use LogicException;
use App\Repositories\Eloquent\ElqCommonRepository;
use Illuminate\Database\Eloquent\Model;

class ElqUserRepository implements UserRepository, Modifiable
{
    private readonly Model $model;
    private readonly ElqCommonRepository $commonRepo;

    public function __construct()
    {
        $this->model = new ElqUser();
        $this->commonRepo = new ElqCommonRepository($this->model);
    }
    public function find(int $id): User
    {
        $result = $this->commonRepo->find($id);
        if (!($result instanceof User)) {
            throw new LogicException();
        }
        return $result;
    }

    public function save(User $user): User
    {
        return $this->commonRepo->save($user, $this);
    }

    public function create(Entity $user): Entity
    {
        if (!($user instanceof User)) {
            throw new LogicException();
        }
        $elqUser = new ElqUser();
        $elqUser->remember_token = $user->remember_token;
        $elqUser->save();

        $elqUserDetail = new ElqUserDetail();
        $elqUserDetail->account_name = $user->account_name;
        $elqUserDetail->name = $user->name;
        $elqUserDetail->email = $user->email;
        $elqUserDetail->password = Hash::make($user->password);

        $elqUser->userDetail()->save($elqUserDetail);
        return $elqUser->toEntity();
    }

    public function update(Entity $user): Entity
    {
        if (!($user instanceof User)) {
            throw new LogicException();
        }
        $elqUser = ElqUser::findOrFail($user->id->value());
        /** @var ElqUser $elqUser */
        $elqUser->userDetail->account_name = $user->account_name;
        $elqUser->userDetail->name = $user->name;
        $elqUser->userDetail->email = $user->email;
        $elqUser->userDetail->password = Hash::make($user->password);
        $elqUser->remember_token = $user->remember_token;
        $elqUser->save();
        return $elqUser->toEntity();
    }

    /**
     * @param array<string, mixed> $where
     * @return User | null
     */
    public function findOneBy(array $where): ?User
    {
        $result = $this->commonRepo->findOneBy($this->createWhereQuery($where));
        if (!($result instanceof User)) {
            return null;
        }
        return $result;
    }

    /**
     * @param array<string, mixed> $where
     * @return Collection<int, User>
     */
    public function findAllBy(array $where): Collection
    {
        return $this->commonRepo->findAllBy($this->createWhereQuery($where));
    }

    /**
     * @param array<string, array> $whereIn
     * @return Collection<int, User>    key:ID
     */
    public function findIn(array $whereIn): Collection
    {
        return $this->commonRepo->findIn(($whereIn));
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
}