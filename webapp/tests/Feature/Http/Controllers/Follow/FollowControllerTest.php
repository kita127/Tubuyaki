<?php

namespace Tests\Feature\Http\Controllers\Follow;

use App\Entities\Identifiable\Unidentified;
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
        $response = $this->actingAs($loginUser)->get("/api/users/{$hoge->id->value()}/following");

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
        $response = $this->actingAs($loginUser)->get("/api/users/{$fuga->id->value()}/followers");

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
        $response = $this->actingAs($loginUser)->post("/api/users/{$fuga->id->value()}/following", []);

        // 検証
        $response->assertStatus(201);

        $followers = $this->followerRepo->findAllBy([]);
        $id = $followers->first()->id;
        $this->assertSame(
            [
                $id->value() => [
                    'id' => $id->value(),
                    'user_id' => $hoge->id->value(),
                    'followee_id' => $fuga->id->value(),
                ],
            ],
            $followers->toArray(),
        );
    }

    /**
     * FollowController::unfollow
     */
    public function test04_01_フォローしているユーザーのフォローを解除する(): void
    {
        // 準備
        /** @var User $hoge */
        /** @var User $fuga */
        [$hoge, $fuga] = $this->createUsers();
        $this->createFollowingRelation($hoge, $fuga);
        $loginUser = new TubuyakiUser($hoge);

        // 実行
        $response = $this->actingAs($loginUser)->delete("/api/users/{$fuga->id->value()}/following", []);

        // 検証
        $response->assertStatus(204);   // No Content

        $followers = $this->followerRepo->findAllBy([]);
        $this->assertCount(0, $followers);
    }

    /**
     * FollowController::getMyFollowees
     */
    public function test05_01_自分がフォローしているユーザーの一覧を取得する(): void
    {
        // 準備
        /** @var User $me */
        /** @var User $fuga */
        [$me, $fuga] = $this->createUsers();
        $this->createFollowingRelation($me, $fuga);
        $loginUser = new TubuyakiUser($me);

        // 実行
        $response = $this->actingAs($loginUser)->get("/api/users/me/following");

        // 検証
        $response->assertStatus(200);
        $this->assertSame(
            [
                'followees' => [
                    [
                        'id' => $fuga->id->value(),
                        'account_name' => 'fuga',
                        'name' => 'ふが次郎',
                    ],
                ],
            ],
            $response->json()
        );
    }

    /**
     * FollowController::getMyFollowers
     */
    public function test06_01_自分をフォローしているユーザーの一覧を取得する(): void
    {
        // 準備
        /** @var User $me */
        /** @var User $fuga */
        [$me, $fuga] = $this->createUsers();
        $this->createFollowingRelation($fuga, $me);
        $loginUser = new TubuyakiUser($me);

        // 実行
        $response = $this->actingAs($loginUser)->get("/api/users/me/followers");

        // 検証
        $response->assertStatus(200);
        $this->assertSame(
            [
                'followers' => [
                    [
                        'id' => $fuga->id->value(),
                        'account_name' => 'fuga',
                        'name' => 'ふが次郎',
                    ],
                ],
            ],
            $response->json()
        );
    }

    /**
     * @return Collection<User>
     */
    private function createUsers(): Collection
    {
        $u1 = new User(new Unidentified(), 'hoge', 'ほげ太郎', 'hoge@example.net', 'hogehoge');
        $u2 = new User(new Unidentified(), 'fuga', 'ふが次郎', 'fuga@example.net', 'fugafuga');
        $u1 = $this->userRepo->save($u1);
        $u2 = $this->userRepo->save($u2);
        return collect([$u1, $u2]);
    }

    private function createFollowingRelation(User $follower, User $followee): void
    {
        $f = new Follower(
            id: new Unidentified(),
            user_id: $follower->id->value(),
            followee_id: $followee->id->value()
        );
        $this->followerRepo->save($f);
    }
}
