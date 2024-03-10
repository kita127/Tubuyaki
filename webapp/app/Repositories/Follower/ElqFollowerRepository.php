<?php

namespace App\Repositories\Follower;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use App\Entities\Follower;
use App\Models\Follower as ElqFollower;
use App\Entities\Identifiable\Identified;

class ElqFollowerRepository implements FollowerRepository
{
    /**
     * @param Follower $follower
     * @return Follower
     */
    public function save(Follower $follower): Follower
    {
        if ($follower->id->isIdentified()) {
            $elqFollower = $this->update($follower);
        } else {
            $elqFollower = $this->create($follower);
        }
        return $elqFollower->toEntity();
    }

    /**
     * @param array<string, mixed> $where
     * @return Collection<int, Follower>    key:ID
     */
    public function findAllBy(array $where): Collection
    {
        $query = $this->createWhereQuery($where);
        $followers = $query->get();
        return $followers->map(fn(ElqFollower $f) => $f->toEntity())->keyBy('id');
    }

    /**
     * @param array<string, mixed> $where
     * @return Builder
     */
    private function createWhereQuery(array $where): Builder
    {
        $query = ElqFollower::query();
        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }
        return $query;
    }

    private function create(Follower $follower): ElqFollower
    {
        $ef = new ElqFollower([
            'user_id' => $follower->user_id,
            'followee_id' => $follower->followee_id,
        ]);
        $ef->save();
        return $ef;
    }

    private function update(Follower $follower): ElqFollower
    {
        $ef = ElqFollower::findOrFail($follower->id->value());
        $ef->user_id = $follower->user_id;
        $ef->followee_id = $follower->followee_id;
        return $ef;
    }
}