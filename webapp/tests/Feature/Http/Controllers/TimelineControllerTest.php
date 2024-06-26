<?php

namespace Tests\Feature\Http\Controllers;

use App\Entities\Identifiable\Identified;
use App\Entities\Identifiable\Unidentified;
use App\Entities\Tweet;
use App\Entities\TweetType;
use App\Repositories\Follower\FollowerRepository;
use App\Repositories\Tweet\TweetRepository;
use App\Services\TubuyakiUser;
use App\Services\Tweet\TweetService;
use Carbon\Carbon;
use Faker\Factory;
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
                            'id' => $user2Tweet->id->value(),
                            'text' => 'ユーザ2のつぶやき',
                            'tweet_type' => 'normal',
                            'user' => [
                                'id' => $user2->id->value(),
                                'account_name' => $user2->accountName(),
                                'name' => $user2->name(),
                            ],
                            'target_id' => null,
                            'created_at' => $user2Tweet->created_at,
                            'updated_at' => $user2Tweet->updated_at,
                        ],
                        [
                            'id' => $user1Tweet->id->value(),
                            'text' => '自分のつぶやき',
                            'tweet_type' => 'normal',
                            'user' => [
                                'id' => $user1->id->value(),
                                'account_name' => $user1->accountName(),
                                'name' => $user1->name(),
                            ],
                            'target_id' => null,
                            'created_at' => $user1Tweet->created_at,
                            'updated_at' => $user1Tweet->updated_at,
                        ],
                    ],
                    'next' => null,
                ],
            ],
            $response->json(),
        );
    }

    /**
     * TimelineController::getTimeline
     *
     * @return void
     */
    public function test01_02_タイムラインは新しいものから順に取得する(): void
    {
        // 準備
        $users = $this->userAssistance->createUsers(2);
        /** @var TubuyakiUser $user1 */
        $user1 = $users->shift();
        $user2 = $users->shift();
        $user1->follow($user2, $this->followerRepository);  // user2をフォローする

        $now = Carbon::now();
        $this->createTweet($user1, '4: 自分のつぶやき1');
        TimeUtil::addTheDate(1, $now);
        $this->createTweet($user2, '3: ユーザー2のつぶやき1');
        TimeUtil::addTheDate(2, $now);
        $this->createTweet($user2, '2: ユーザー2のつぶやき2');
        TimeUtil::addTheDate(3, $now);
        $this->createTweet($user1, '1: 自分のつぶやき2');

        // 実行
        $response = $this->actingAs($user1)->get("api/users/{$user1->id->value()}/timeline");

        // 検証
        $response->assertStatus(200);
        $this->assertSame(
            [
                '1: 自分のつぶやき2',
                '2: ユーザー2のつぶやき2',
                '3: ユーザー2のつぶやき1',
                '4: 自分のつぶやき1',
            ],
            array_map(fn ($tweet) => $tweet['text'], $response->json()['contents']['tweets']),
        );
    }

    /**
     * TimelineController::getTimeline
     *
     * @return void
     */
    public function test01_03_タイムラインは取得するインデックス件数を指定できる(): void
    {
        // 準備
        $faker = Factory::create('ja_JP');

        /** @var TubuyakiUser $user1 */
        $user = $this->userAssistance->createUsers(1)->shift();
        $expected = collect([]);
        for ($i = 0; $i < 30; $i++) {
            $content = $faker->realText();
            $expected->push($this->createTweet($user, $content));
        }

        $actualTweets = [];
        $index = 0;
        $count = 10;
        do {
            // 実行
            $response = $this->actingAs($user)->get("api/users/{$user->id->value()}/timeline?index={$index}&count={$count}");
            // 検証
            $response->assertStatus(200);
            $content = $response->json();
            $tweets = $content['contents']['tweets'];
            $next = $content['contents']['next'];

            $this->assertCount(10, $tweets);
            $actualTweets = array_merge($actualTweets, $tweets);
            if ($next) {
                $this->assertSame($index + $count, $next);
            }
            $index = $next;
        } while ($next);

        // 合計30件あって要素の被りもないことを確認する
        $this->assertCount(30, $actualTweets);
        $idList = array_map(fn ($x) => $x['id'], $actualTweets);
        $idList = array_unique($idList);
        $this->assertCount(30, $idList, '重複していないことを確認する');
    }

    /**
     * TimelineController::getTimeline
     *
     * @return void
     */
    public function test01_04_タイムラインでいろんなタイプのつぶやきを取得(): void
    {
        // 準備
        $users = $this->userAssistance->createUsers(2);
        /** @var TubuyakiUser $user1 */
        $user1 = $users->shift();
        $user2 = $users->shift();

        $user1Tweet = $this->createTweet($user1, '自分のつぶやき');
        $user2Tweet = $this->createTweet($user2, 'ユーザー2のつぶやき');
        $user1->follow($user2, $this->followerRepository);  // user2をフォローする

        /** @var TweetService $tweetService */
        $user1Reply = $this->createTweet($user1, 'ユーザー2のつぶやきへの返信', TweetType::Reply, $user2Tweet->id->value());

        $this->tweetRepository->retweet($user2Tweet, $user1->getEntity());

        // 実行
        $response = $this->actingAs($user1)->get("api/users/{$user1->id->value()}/timeline");

        // 検証
        $response->assertStatus(200);
        $content = $response->json();
        $this->assertSame(
            [
                'contents' => [
                    'tweets' => [
                        [
                            'id' => $content['contents']['tweets'][0]['id'],
                            'text' => '',
                            'tweet_type' => 'retweet',
                            'user' => [
                                'id' => $user1->id->value(),
                                'account_name' => $user1->accountName(),
                                'name' => $user1->name(),
                            ],
                            'target_id' => $user2Tweet->id->value(),
                            'created_at' => $content['contents']['tweets'][0]['created_at'],
                            'updated_at' => $content['contents']['tweets'][0]['updated_at'],
                        ],
                        [
                            'id' => $user1Reply->id->value(),
                            'text' => 'ユーザー2のつぶやきへの返信',
                            'tweet_type' => 'reply',
                            'user' => [
                                'id' => $user1->id->value(),
                                'account_name' => $user1->accountName(),
                                'name' => $user1->name(),
                            ],
                            'target_id' => $user2Tweet->id->value(),
                            'created_at' => $user1Reply->created_at,
                            'updated_at' => $user1Reply->updated_at,
                        ],
                        [
                            'id' => $user2Tweet->id->value(),
                            'text' => 'ユーザー2のつぶやき',
                            'tweet_type' => 'normal',
                            'user' => [
                                'id' => $user2->id->value(),
                                'account_name' => $user2->accountName(),
                                'name' => $user2->name(),
                            ],
                            'target_id' => null,
                            'created_at' => $user2Tweet->created_at,
                            'updated_at' => $user2Tweet->updated_at,
                        ],
                        [
                            'id' => $user1Tweet->id->value(),
                            'text' => '自分のつぶやき',
                            'tweet_type' => 'normal',
                            'user' => [
                                'id' => $user1->id->value(),
                                'account_name' => $user1->accountName(),
                                'name' => $user1->name(),
                            ],
                            'target_id' => null,
                            'created_at' => $user1Tweet->created_at,
                            'updated_at' => $user1Tweet->updated_at,
                        ],
                    ],
                    'next' => null,
                ],
            ],
            $content,
        );
    }

    private function createTweet(
        TubuyakiUser $user,
        string $content,
        TweetType $type = TweetType::Normal,
        ?int $targetId = null,
    ): Tweet {
        $targetIdObj = $targetId ? new Identified($targetId) : new Unidentified();
        $tweet = new Tweet(new Unidentified(), $user->id->value(), $type, $content, $targetIdObj);
        return $this->tweetRepository->save($tweet);
    }
}
