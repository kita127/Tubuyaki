<?php

namespace Tests\Feature\Http\Controllers;

use App\Entities\Identifiable\Unidentified;
use App\Entities\Tweet;
use App\Entities\TweetType;
use App\Repositories\Tweet\TweetRepository;
use App\Services\TubuyakiUser;
use Tests\Lib\UserAssistance;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TimelineControllerTest extends TestCase
{
    use DatabaseTransactions;

    private readonly UserAssistance $userAssistance;
    private readonly TweetRepository $tweetRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userAssistance = app()->make(UserAssistance::class);
        $this->tweetRepository = app()->make(TweetRepository::class);
    }

    /**
     * TimelineController::getTimeline
     *
     * @return void
     */
    public function test01_01_タイムラインを取得する(): void
    {
        // 準備
        $user = $this->userAssistance->createUser();
        $tweet = $this->createTweet($user, 'つぶやきの内容');

        // 実行
        $response = $this->actingAs($user)->get("api/users/{$user->id->value()}/timeline");

        // 検証
        $response->assertStatus(200);
        $this->assertSame([], $response->json());
    }

    private function createTweet(TubuyakiUser $user, string $content): Tweet
    {
        $tweet = new Tweet(new Unidentified(), $user->id->value(), TweetType::Normal, $content);
        return $this->tweetRepository->save($tweet);
    }
}
