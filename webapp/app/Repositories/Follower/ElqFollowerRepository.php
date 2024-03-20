<?php

namespace App\Repositories\Follower;

use App\Entities\Entity;
use App\Models\BaseModel;
use App\Repositories\Interface\Modifiable;
use Illuminate\Support\Collection;
use App\Entities\Follower;
use App\Models\Follower as ElqFollower;
use App\Repositories\Eloquent\ElqCommonRepository;
use LogicException;

class ElqFollowerRepository implements FollowerRepository, Modifiable
{
    private readonly BaseModel $model;
    private readonly ElqCommonRepository $commonRepo;

    public function __construct()
    {
        $this->model = new ElqFollower();
        $this->commonRepo = new ElqCommonRepository($this->model);
    }

    /**
     * @param Follower $follower
     * @return Follower
     */
    public function save(Follower $follower): Follower
    {
        $result = $this->commonRepo->save($follower, $this);
        if (!($result instanceof Follower)) {
            throw new LogicException();
        }
        return $result;
    }

    /**
     * @param array<string, mixed> $where
     * @return Collection<int, Follower>    key:ID
     */
    public function findAllBy(array $where): Collection
    {
        return $this->commonRepo->findAllBy($where);
    }

    /**
     * @param array<string, mixed> $where
     * @return Follower
     */
    public function findOneBy(array $where): ?Follower
    {
        $result = $this->commonRepo->findOneBy($where);
        if (!($result instanceof Follower)) {
            return null;
        }
        return $result;
    }

    public function delete(Follower $follower): bool
    {
        return $this->commonRepo->delete($follower);
    }

    public function create(Entity $follower): Entity
    {
        if (!($follower instanceof Follower)) {
            throw new LogicException;
        }
        $ef = new ElqFollower([
            'user_id' => $follower->user_id,
            'followee_id' => $follower->followee_id,
        ]);
        $ef->save();
        return $ef->toEntity();
    }

    public function update(Entity $follower): Entity
    {
        if (!($follower instanceof Follower)) {
            throw new LogicException;
        }
        $ef = ElqFollower::findOrFail($follower->id->value());
        $ef->user_id = $follower->user_id;
        $ef->followee_id = $follower->followee_id;
        $ef->save();
        return $ef->toEntity();
    }
}