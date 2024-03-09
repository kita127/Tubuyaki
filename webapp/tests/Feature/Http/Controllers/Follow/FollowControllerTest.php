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

    /**
     * FollowController::index
     */
    public function test01_01_フォローしているユーザーの一覧を取得する(): void
    {
        // 準備
        [$hoge, $fuga] = $this->createUsers();
        $this->createFollowingRelation($hoge, $fuga);
        $loginUser = new TubuyakiUser($hoge);

        // 実行
        $response = $this->actingAs($loginUser)->get("/api/users/{$hoge->id}/following");

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

    public function test02_01_フォローされているユーザーの一覧を取得する(): void
    {
        // 準備
        [$hoge, $fuga] = $this->createUsers();
        $this->createFollowingRelation($hoge, $fuga);
        $loginUser = new TubuyakiUser($fuga);

        // 実行
        $response = $this->actingAs($loginUser)->get("/api/users/{$fuga->id}/followers");

        // 検証
        $response->assertStatus(200);
        $content = $response->json();
        $this->assertSame(
            [
                'followers' => [
                    [
                        'id' => $content['followers'][0]['id'],
                        'account_name' => 'hoge',
                        'name' => 'ほげ太郎',
                    ],
                ],
            ],
            $content
        );
    }

    public function test03_01_ユーザーをフォローする(): void
    {
        // 準備
        /** @var User $hoge */
        /** @var User $fuga */
        [$hoge, $fuga] = $this->createUsers();
        $loginUser = new TubuyakiUser($hoge);

        // 実行
        $response = $this->actingAs($loginUser)->post("/api/users/{$fuga->id}/following", []);

        // 検証
        $response->assertStatus(201);

        $followers = $this->followerRepo->findAllBy([]);
        $id = $followers->first()->id;
        $this->assertSame(
            [
                $id => [
                    'id' => $id,
                    'user_id' => $hoge->id,
                    'followee_id' => $fuga->id,
                ],
            ],
            $followers->toArray(),
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
