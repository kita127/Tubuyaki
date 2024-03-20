<?php

namespace Tests\Feature\Http\Controllers;

use App\Entities\Identifiable\Unidentified;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Lib\UserAssistance;
use App\Services\TubuyakiUser;
use App\Entities\Tweet;
use App\Repositories\Tweet\TweetRepository;

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
        $tweet = new Tweet(new Unidentified(), $user->id->value(), 'つぶやきの内容');
        $tweet = $this->tweetRepository->save($tweet);

        // 実行
        $response = $this->actingAs($user)->get("api/users/me/tweets");

        // 検証
        $response->assertStatus(200);
        $content = $response->json();
        $this->assertSame(
            [
                'tweets' => [
                    [
                        'text' => 'つぶやきの内容',
                        'created_at' => $tweet->created_at,
                        'updated_at' => $tweet->updated_at,
                    ],
                ],
            ],
            $content
        );
    }
}
