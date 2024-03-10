<?php

namespace Tests\Feature;

use App\Repositories\User\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Lib\UserAssistance;
use App\Services\TubuyakiUser;

class TweetControllerTest extends TestCase
{
    use DatabaseTransactions;

    private readonly TweetRepository $tweetRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tweetRepository = app()->make(TweetRepository::class);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test01_01_自分のつぶやき一覧を取得する(): void
    {
        // 準備
        /** @var UserAssistance $userAssistance */
        $userAssistance = app()->make(UserAssistance::class);
        $user = $userAssistance->createUser();
        $tweet = new Tweet(['user_id' => $user->id->value(), 'text' => 'つぶやきの内容']);
        $this->tweetRepository->save($tweet);
        $loginUser = new TubuyakiUser($user);

        // 実行
        $response = $this->actingAs($loginUser)->get("api/users/me/tweets");

        // 検証
        $response->assertStatus(200);
        $content = $response->json();
        $this->assertSame(
            [
                'tweets' => [
                    'text' => 'つぶやきの内容',
                    'created_at' => '',
                    'updated_at' => '',
                ],
            ],
            $content
        );

    }
}
