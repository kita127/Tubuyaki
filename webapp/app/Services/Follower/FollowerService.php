<?php

namespace App\Services\Follower;

use App\Entities\Follower;
use App\Entities\User;
use App\Repositories\Follower\FollowerRepository;
use App\Repositories\User\UserRepository;
use App\Services\TubuyakiUser;
use Illuminate\Support\Collection;

// TODO: FollowServiceにクラス名変更する
class FollowerService
{
    public function __construct(
        private readonly FollowerRepository $followerRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function getFollowees(int $id): Collection
    {
        $followRelations = $this->followerRepository->findAllBy(['user_id' => $id]);
        $followeeIdLs = $followRelations->map(fn(Follower $follower) => $follower->followee_id);
        $followees = $this->userRepository->findIn(['id' => $followeeIdLs->all()]);
        return $followees->map(fn(User $user) => [
            // TODO: ここはクラス化を検討する？
            'id' => $user->id,
            'account_name' => $user->account_name,
            'name' => $user->name,
        ]);
    }

    public function getFollowers(int $id): Collection
    {
        $followRelations = $this->followerRepository->findAllBy(['followee_id' => $id]);
        $followerIdLs = $followRelations->map(fn(Follower $follower) => $follower->user_id);
        $followers = $this->userRepository->findIn(['id' => $followerIdLs->all()]);
        return $followers->map(fn(User $user) => [
            // TODO: ここはクラス化を検討する？
            'id' => $user->id,
            'account_name' => $user->account_name,
            'name' => $user->name,
        ]);
    }

    public function follow(TubuyakiUser $me, User $target): void
    {
        $followRelation = new Follower(null, $me->id, $target->id);
        $this->followerRepository->save($followRelation);
    }
}