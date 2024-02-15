<?php

namespace Tests\Feature\Http\Controllers\Follow;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\TubuyakiUser;
use App\Repositories\User\UserRepository;
use App\Entities\User;
use App\Entities\Follower;
use App\Repositories\Follower\FollowerRepository;
use Illuminate\Support\Collection;
use Tests\TestCase;

class FollowControllerTest extends TestCase
{
    use DatabaseTransactions;

    private readonly UserRepository $userRepo;
    private readonly FollowerRepository $followerRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepo = app()->make(UserRepository::class);
        $this->followerRepo = app()->make(FollowerRepository::class);
    }

    public function test01_01_フォローしているユーザーの一覧を取得する()
    {
        // 準備
        $users = $this->createUsers();
        $this->createFollowingRelation($users[0], $users[1]);
        $loginUser = new TubuyakiUser($users[0]);

        // 実行
        $response = $this->actingAs($loginUser)->get("/api/users/{$users[0]->id}/following");

        // 検証
        $response->assertStatus(200);
        $content = $response->json();
        $this->assertSame(
            [
                'followees' => [
                    [
                        'id' => $content['followees'][0]['id'],
                        'account_name' => 'fuga',
                        'name' => 'ふが次郎',
                    ],
                ],
            ],
            $content
        );
    }

    /**
     * @return Collection<User>
     */
    private function createUsers(): Collection
    {
        $u1 = new User(null, 'hoge', 'ほげ太郎', 'hoge@example.net', 'hogehoge');
        $u2 = new User(null, 'fuga', 'ふが次郎', 'fuga@example.net', 'fugafuga');
        $u1 = $this->userRepo->save($u1);
        $u2 = $this->userRepo->save($u2);
        return collect([$u1, $u2]);
    }

    private function createFollowingRelation(User $follower, User $followee): void
    {
        $f = new Follower(null, $follower->id, $followee->id);
        $this->followerRepo->save($f);
    }
}
