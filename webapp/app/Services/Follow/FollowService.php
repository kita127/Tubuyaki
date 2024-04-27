<?php

namespace App\Services\Follow;

use App\Entities\Follower;
use App\Entities\User;
use App\Repositories\Follower\FollowerRepository;
use App\Repositories\User\UserRepository;
use App\Services\TubuyakiUser;
use Illuminate\Support\Collection;

class FollowService
{
    public function __construct(
        private readonly FollowerRepository $followerRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function getFollowees(TubuyakiUser $user): Collection
    {
        $followRelations = $this->followerRepository->findAllBy(['user_id' => $user->id->value()]);
        $followeeIdLs = $followRelations->map(fn (Follower $follower) => $follower->followee_id);
        $followees = $this->userRepository->findIn(['id' => $followeeIdLs->all()]);
        return $followees->map(fn (User $user) => [
            // TODO: ここはクラス化を検討する？
            'id' => $user->id->value(),
            'account_name' => $user->account_name,
            'name' => $user->name,
        ]);
    }

    public function getFollowers(TubuyakiUser $user): Collection
    {
        $followRelations = $this->followerRepository->findAllBy(['followee_id' => $user->id->value()]);
        $followerIdLs = $followRelations->map(fn (Follower $follower) => $follower->user_id);
        $followers = $this->userRepository->findIn(['id' => $followerIdLs->all()]);
        return $followers->map(fn (User $user) => [
            // TODO: ここはクラス化を検討する？
            'id' => $user->id->value(),
            'account_name' => $user->account_name,
            'name' => $user->name,
        ]);
    }

    public function follow(TubuyakiUser $me, TubuyakiUser $target): void
    {
        $me->follow($target, $this->followerRepository);
    }

    public function unfollow(TubuyakiUser $me, User $target): void
    {
        $relation = $this->followerRepository->findOneBy([
            'user_id' => $me->id->value(),
            'followee_id' => $target->id->value()
        ]);
        if ($relation) {
            $this->followerRepository->delete($relation);
        }
    }
}
