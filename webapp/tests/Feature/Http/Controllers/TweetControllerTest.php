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

    /**
     * TweetController::getMyTweets
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
        $content = $response->json();
        $this->assertSame(
            [
                'tweets' => [
                    [
                        'text' => '他人のつぶやき',
                        'created_at' => $tweet->created_at,
                        'updated_at' => $tweet->updated_at,
                    ],
                ],
            ],
            $content
        );
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
        $tweets = $this->tweetRepository->findAllBy(['user_id' => $me->id->value()]);
        $this->assertSame(
            [
                [
                    'id' => $tweets->first()->id->value(),
                    'user_id' => $me->id->value(),
                    'type' => 'normal',
                    'text' => '新規ツイート',
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
        $tweets = $this->createTweets($other, 3, TweetType::Reply);
        foreach ($tweets as $reply) {
            $this->createReplies($reply, $ownTweet);
        }

        // 更新時間を変更する
        // 取得する返信は更新時間の降順になるはず
        $fst = $tweets->get(0);
        $fst = $this->updateTweetWithTime($fst, $this->now->addHour());
        $snd = $tweets->get(1);
        $snd = $this->updateTweetWithTime($snd, $this->now->addHour());
        $thd = $tweets->get(2);
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
                        'text' => $thd->text,
                        'tweet_type' => 'reply',
                        'user' => [
                            'id' => $other->id->value(),
                            'account_name' => $other->accountName(),
                            'name' => $other->name(),
                        ],
                        'created_at' => $thd->created_at,
                        'updated_at' => $thd->updated_at,
                    ],
                    [
                        'id' => $snd->id->value(),
                        'text' => $snd->text,
                        'tweet_type' => 'reply',
                        'user' => [
                            'id' => $other->id->value(),
                            'account_name' => $other->accountName(),
                            'name' => $other->name(),
                        ],
                        'created_at' => $snd->created_at,
                        'updated_at' => $snd->updated_at,
                    ],
                    [
                        'id' => $fst->id->value(),
                        'text' => $fst->text,
                        'tweet_type' => 'reply',
                        'user' => [
                            'id' => $other->id->value(),
                            'account_name' => $other->accountName(),
                            'name' => $other->name(),
                        ],
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
        $tweet = new Tweet(new Unidentified(), $user->id->value(), $type, $content);
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
     * @param Tweet $tweet
     * @param Collection<Tweet> $replies
     */
    private function createReplies(Tweet $reply, Tweet $toTweet): void
    {
        $this->tweetRepository->reply($reply, $toTweet);
    }
}
