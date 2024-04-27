<?php

namespace Tests\Feature\Http\Controllers;

use App\Entities\Identifiable\Unidentified;
use App\Entities\Tweet;
use App\Entities\TweetType;
use App\Repositories\Follower\FollowerRepository;
use App\Repositories\Tweet\TweetRepository;
use App\Services\TubuyakiUser;
use Carbon\Carbon;
use Tests\Lib\UserAssistance;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Lib\TimeUtil;

class TimelineControllerTest extends TestCase
{
    use DatabaseTransactions;

    private readonly UserAssistance $userAssistance;
    private readonly TweetRepository $tweetRepository;
    private readonly FollowerRepository $followerRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $now = new Carbon('2022-07-29 00:00:00');
        Carbon::setTestNow($now);

        $this->userAssistance = app()->make(UserAssistance::class);
        $this->tweetRepository = app()->make(TweetRepository::class);
        $this->followerRepository = app()->make(FollowerRepository::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * TimelineController::getTimeline
     *
     * @return void
     */
    public function test01_01_タイムラインを取得する(): void
    {
        // 準備
        $users = $this->userAssistance->createUsers(2);
        /** @var TubuyakiUser $user1 */
        $user1 = $users->shift();
        $user2 = $users->shift();

        $user1Tweet = $this->createTweet($user1, '自分のつぶやき');
        $user2Tweet = $this->createTweet($user2, 'ユーザ2のつぶやき');
        $user1->follow($user2, $this->followerRepository);  // user2をフォローする

        // 実行
        $response = $this->actingAs($user1)->get("api/users/{$user1->id->value()}/timeline");

        // 検証
        $response->assertStatus(200);
        $this->assertSame(
            [
                'contents' => [
                    'tweets' => [
                        [
                            'id' => $user1Tweet->id->value(),
                            'text' => '自分のつぶやき',
                            'tweet_type' => 'normal',
                            'user' => [
                                'id' => $user1->id->value(),
                                'account_name' => $user1->accountName(),
                                'name' => $user1->name(),
                            ],
                            'created_at' => $user1Tweet->created_at,
                            'updated_at' => $user1Tweet->updated_at,
                        ],
                        [
                            'id' => $user2Tweet->id->value(),
                            'text' => 'ユーザ2のつぶやき',
                            'tweet_type' => 'normal',
                            'user' => [
                                'id' => $user2->id->value(),
                                'account_name' => $user2->accountName(),
                                'name' => $user2->name(),
                            ],
                            'created_at' => $user2Tweet->created_at,
                            'updated_at' => $user2Tweet->updated_at,
                        ],
                    ],
                ],
            ],
            $response->json(),
        );
    }

    private function createTweet(TubuyakiUser $user, string $content): Tweet
    {
        $tweet = new Tweet(new Unidentified(), $user->id->value(), TweetType::Normal, $content);
        return $this->tweetRepository->save($tweet);
    }
}
