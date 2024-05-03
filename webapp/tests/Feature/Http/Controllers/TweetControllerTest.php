<?php

namespace Tests\Feature\Http\Controllers;

use App\Entities\Identifiable\Unidentified;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Lib\UserAssistance;
use App\Services\TubuyakiUser;
use App\Entities\Tweet;
use App\Entities\TweetType;
use App\Repositories\Tweet\TweetRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TweetControllerTest extends TestCase
{
    use DatabaseTransactions;

    private Carbon $now;
    private readonly TweetRepository $tweetRepository;
    private readonly UserAssistance $userAssistance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->now = new Carbon('2024/07/29 12:00:00');
        Carbon::setTestNow($this->now);

        $this->tweetRepository = app()->make(TweetRepository::class);
        $this->userAssistance = app()->make(UserAssistance::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * TweetController::getMyTweets
     * @return void
     */
    public function test01_01_自分のつぶやき一覧を取得する(): void
    {
        // 準備
        /** @var UserAssistance $userAssistance */
        $user = $this->userAssistance->createUser();
        $tweet = $this->createTweet($user, 'つぶやきの内容', TweetType::Normal);

        // 実行
        $response = $this->actingAs($user)->get("api/users/me/tweets");

        // 検証
        $response->assertStatus(200);
        $content = $response->json()['contents'];
        $this->assertSame(
            [
                'tweets' => [
                    [
                        'text' => 'つぶやきの内容',
                        'created_at' => $tweet->created_at,
                        'updated_at' => $tweet->updated_at,
                    ],
                ],
                'next' => null, // 次がなければnull
            ],
            $content
        );
    }

    /**
     * TweetController::getMyTweets
     * @return void
     */
    public function test01_02_自分のつぶやき一覧を取得する_ページネーション(): void
    {
        // 準備
        /** @var UserAssistance $userAssistance */
        $user = $this->userAssistance->createUser();
        $expected = $this->createTweets($user, 30, TweetType::Normal);
        $expected = $expected->sortByDesc(function (Tweet $item, $key) {
            return $item->updated_at . $item->id->value();
        });

        $index = 0;
        $allTweets = [];
        do {
            // 実行
            $response = $this->actingAs($user)->get("api/users/me/tweets?index={$index}&count=10");

            // 検証
            $response->assertStatus(200);
            $content = $response->json()['contents'];
            $tweets = $content['tweets'];
            $this->assertCount(10, $tweets);
            $allTweets = array_merge($allTweets, $tweets);
            $next = $content['next'];
            if ($next) {
                $this->assertSame($index + 10, $next);
            }
            $index = $next;
        } while ($index);

        $expectedTexts = $expected->pluck('text')->all();
        $acualTexts = array_map(fn ($x) => $x['text'], $allTweets);
        $this->assertSame($expectedTexts, $acualTexts);
    }

    /**
     * TweetController::getTweets
     * @return void
     */
    public function test02_01_任意のユーザーのつぶやき一覧を取得する(): void
    {
        // 準備
        /** @var UserAssistance $userAssistance */
        $me = $this->userAssistance->createUser();
        $other = $this->userAssistance->createUser('other', '他人ユーザ', 'other@example.net', 'password');
        $tweet = $this->createTweet($other, '他人のつぶやき', TweetType::Normal);

        // 実行
        $response = $this->actingAs($me)->get("api/users/{$other->id->value()}/tweets");

        // 検証
        $response->assertStatus(200);
        $content = $response->json()['contents'];
        $this->assertSame(
            [
                'tweets' => [
                    [
                        'text' => '他人のつぶやき',
                        'created_at' => $tweet->created_at,
                        'updated_at' => $tweet->updated_at,
                    ],
                ],
                'next' => null,
            ],
            $content
        );
    }

    /**
     * TweetController::getTweets
     * @return void
     */
    public function test02_02_任意のユーザーのつぶやき一覧を取得する_ページネーション(): void
    {
        // 準備
        /** @var UserAssistance $userAssistance */
        $me = $this->userAssistance->createUser();
        $other = $this->userAssistance->createUser('other', '他人ユーザ', 'other@example.net', 'password');
        $expected = $this->createTweets($other, 30, TweetType::Normal);
        $expected = $expected->sortByDesc(function (Tweet $item, $key) {
            return $item->updated_at . $item->id->value();
        });

        $allTweets = [];
        $index = 0;
        do {
            // 実行
            $response = $this->actingAs($me)->get("api/users/{$other->id->value()}/tweets?index={$index}&count=10");

            // 検証
            $response->assertStatus(200);
            $content = $response->json()['contents'];
            $tweets = $content['tweets'];
            $next = $content['next'];
            $this->assertCount(10, $tweets);
            $allTweets = array_merge($allTweets, $tweets);
            if ($next) {
                $this->assertSame($index + 10, $next);
            }
            $index = $next;
        } while ($index);
        $expectedTexts = $expected->pluck('text')->all();
        $allTexts = array_map(fn ($x) => $x['text'], $allTweets);
        $this->assertSame($expectedTexts, $allTexts);
    }

    /**
     * TweetController::post
     */
    public function test03_01_つぶやきを投稿する(): void
    {
        $me = $this->userAssistance->createUser();
        $response = $this->actingAs($me)->post(
            "api/tweets",
            [
                'text' => '新規ツイート',
            ]
        );
        $response->assertStatus(201);
        /** @var Collection<Tweet> $tweets */
        $tweets = $this->tweetRepository->findAllBy(['user_id' => $me->id->value()]);
        $this->assertSame(
            [
                [
                    'id' => $tweets->first()->id->value(),
                    'user_id' => $me->id->value(),
                    'type' => 'normal',
                    'text' => '新規ツイート',
                    'target_id' => null,
                    'created_at' => $tweets->first()->created_at,
                    'updated_at' => $tweets->first()->updated_at,
                ],
            ],
            $tweets->values()->toArray()
        );
    }

    /**
     * TweetController::getReplies
     */
    public function test04_01_つぶやきの返信一覧を取得する(): void
    {
        // 準備
        $me = $this->userAssistance->createUser();
        $ownTweet = $this->createTweet($me, '自分のツイート', TweetType::Normal);
        $other = $this->userAssistance->createUsers(1)->first();
        $replies = collect([]);
        for ($i = 0; $i < 3; $i++) {
            $replies->push($this->createReplies($other, $ownTweet));
        }

        // 更新時間を変更する
        // 取得する返信は更新時間の降順になるはず
        $fst = $replies->get(0);
        $fst = $this->updateTweetWithTime($fst, $this->now->addHour());
        $snd = $replies->get(1);
        $snd = $this->updateTweetWithTime($snd, $this->now->addHour());
        $thd = $replies->get(2);
        $thd = $this->updateTweetWithTime($thd, $this->now->addHour());

        // 実行
        $response = $this->actingAs($me)->get("api/tweets/{$ownTweet->id->value()}/replies");

        // 検証
        $response->assertStatus(200);
        $content = $response->json();
        $this->assertSame(
            [
                'replies' => [
                    [
                        'id' => $thd->id->value(),
                        'user' => [
                            'id' => $other->id->value(),
                            'account_name' => $other->accountName(),
                            'name' => $other->name(),
                        ],
                        'text' => $thd->text,
                        'tweet_type' => 'reply',
                        'target_id' => $ownTweet->id->value(),
                        'created_at' => $thd->created_at,
                        'updated_at' => $thd->updated_at,
                    ],
                    [
                        'id' => $snd->id->value(),
                        'user' => [
                            'id' => $other->id->value(),
                            'account_name' => $other->accountName(),
                            'name' => $other->name(),
                        ],
                        'text' => $snd->text,
                        'tweet_type' => 'reply',
                        'target_id' => $ownTweet->id->value(),
                        'created_at' => $snd->created_at,
                        'updated_at' => $snd->updated_at,
                    ],
                    [
                        'id' => $fst->id->value(),
                        'user' => [
                            'id' => $other->id->value(),
                            'account_name' => $other->accountName(),
                            'name' => $other->name(),
                        ],
                        'text' => $fst->text,
                        'tweet_type' => 'reply',
                        'target_id' => $ownTweet->id->value(),
                        'created_at' => $fst->created_at,
                        'updated_at' => $fst->updated_at,
                    ],
                ],
            ],
            $content
        );
    }

    /**
     * TweetController::reply
     */
    public function test05_01_つぶやきに返信する(): void
    {
        // 準備
        $me = $this->userAssistance->createUser();
        $other = $this->userAssistance->createUsers(1)->first();
        /** @var Tweet $tweet */
        $tweet = $this->createTweets($other, 1, TweetType::Normal)->first();

        // 実行
        $response = $this->actingAs($me)->post(
            "api/tweets/{$tweet->id->value()}/replies",
            [
                'text' => '返信つぶやき',
            ],
        );

        // 検証
        $response->assertStatus(201);
        $replies = $this->tweetRepository->findAllReplies($tweet);
        $reply = $replies->first();
        $this->assertSame(
            [
                [
                    'id' => $reply->id->value(),
                    'user_id' => $me->id->value(),
                    'type' => 'reply',
                    'text' => '返信つぶやき',
                    'target_id' => $tweet->id->value(),
                    'created_at' => $reply->created_at,
                    'updated_at' => $reply->updated_at,
                ],
            ],
            $replies->toArray()
        );
    }

    /**
     * TweetController::retweet
     */
    public function test06_01_つぶやきにリツイートする(): void
    {
        // 準備
        $me = $this->userAssistance->createUser();
        $other = $this->userAssistance->createUsers(1)->first();
        /** @var Tweet $othersTweet */
        $othersTweet = $this->createTweets($other, 1, TweetType::Normal)->first();

        // 実行
        $response = $this->actingAs($me)->post("api/tweets/{$othersTweet->id->value()}/retweet", []);

        // 検証
        // TODO: リツイートした時間の降順で取得できることを確認する
        $response->assertStatus(201);
        $users = $this->tweetRepository->findRetweetUsers($othersTweet);
        $this->assertSame(
            [
                [
                    'id' => $me->id->value(),
                    'account_name' => $me->accountName(),
                    'name' => $me->name(),
                    'email' => 'test@example.net',
                ],
            ],
            $users->toArray()
        );

        // リツイートとして新しいつぶやきが作成されている
        $tweet = $this->tweetRepository->findAllBy(['user_id' => $me->id->value()])->first();
        $this->assertSame($me->id->value(), $tweet->user_id);
        $this->assertSame('retweet', $tweet->type->value);
    }

    public function test07_01_つぶやきIDを指定してつぶやきの詳細を取得する(): void
    {
        // 準備
        /** @var UserAssistance $userAssistance */
        $user = $this->userAssistance->createUser();
        $tweet = $this->createTweet($user, 'つぶやきの内容', TweetType::Normal);

        // 実行
        $response = $this->actingAs($user)->get("api/tweets/{$tweet->id}");

        // 検証
        $response->assertStatus(200);
        $content = $response->json();
        $this->assertSame(
            [
                'tweet' => [
                    'id' => $tweet->id->value(),
                    'text' => 'つぶやきの内容',
                    'tweet_type' => 'normal',
                    'user' => [
                        'id' => $user->id->value(),
                        'account_name' => $user->accountName(),
                        'name' => $user->name(),
                    ],
                    'target_id' => null,
                    'created_at' => $tweet->created_at,
                    'updated_at' => $tweet->updated_at,
                ],
            ],
            $content
        );
    }

    public function test07_02_つぶやきIDを指定してつぶやきの詳細を取得する_リツイート(): void
    {
        // 準備
        /** @var UserAssistance $userAssistance */
        $user = $this->userAssistance->createUser();
        $other = $this->userAssistance->createUsers(1)->first();
        /** @var Tweet $othersTweet */
        $othersTweet = $this->createTweets($other, 1, TweetType::Normal)->first();
        $tweet = $this->tweetRepository->retweet($othersTweet, $user->getEntity());

        // 実行
        $response = $this->actingAs($user)->get("api/tweets/{$tweet->id->value()}");

        // 検証
        $response->assertStatus(200);
        $content = $response->json();
        $this->assertSame(
            [
                'tweet' => [
                    'id' => $tweet->id->value(),
                    'text' => '',
                    'tweet_type' => 'retweet',
                    'user' => [
                        'id' => $user->id->value(),
                        'account_name' => $user->accountName(),
                        'name' => $user->name(),
                    ],
                    'target_id' => $tweet->target_id->value(),
                    'created_at' => $tweet->created_at,
                    'updated_at' => $tweet->updated_at,
                ],
            ],
            $content
        );
    }

    private function updateTweetWithTime(Tweet $tweet, Carbon $time): Tweet
    {
        // 何かしら更新しないと更新されないので
        $tweet->text = $tweet->text . '<更新後>';
        Carbon::setTestNow($time);
        return $this->tweetRepository->save($tweet);
    }

    private function createTweet(TubuyakiUser $user, string $content, TweetType $type): Tweet
    {
        $tweet = new Tweet(new Unidentified(), $user->id->value(), $type, $content, new Unidentified());
        return $this->tweetRepository->save($tweet);
    }

    /**
     * @return Collection<Tweet>
     */
    private function createTweets(TubuyakiUser $user, int $count, TweetType $type): Collection
    {
        $tweets = collect([]);
        for ($i = 0; $i < $count; $i++) {
            $text = fake()->realText(140);
            $t = $this->createTweet($user, $text, $type);
            $tweets->push($t);
        }
        return $tweets;
    }

    /**
     * 
     * @param TubuyakiUser $user 
     * @param Tweet $toTweet 
     * @return void 
     */
    private function createReplies(TubuyakiUser $user, Tweet $toTweet): Tweet
    {
        $text = fake()->realText(140);
        $targetId = $toTweet->id;
        $tweet = new Tweet(new Unidentified(), $user->id->value(), TweetType::Reply, $text, $targetId);
        return $this->tweetRepository->save($tweet);
    }
}
