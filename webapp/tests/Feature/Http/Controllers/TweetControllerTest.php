<?php

namespace Tests\Feature\Http\Controllers;

use App\Entities\Identifiable\Unidentified;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Lib\UserAssistance;
use App\Services\TubuyakiUser;
use App\Entities\Tweet;
use App\Repositories\Tweet\TweetRepository;
use Illuminate\Support\Collection;

class TweetControllerTest extends TestCase
{
    use DatabaseTransactions;

    private readonly TweetRepository $tweetRepository;
    private readonly UserAssistance $userAssistance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tweetRepository = app()->make(TweetRepository::class);
        $this->userAssistance = app()->make(UserAssistance::class);
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
        $tweet = $this->createTweet($user, 'つぶやきの内容');

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
        $tweet = $this->createTweet($other, '他人のつぶやき');

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
        $ownTweet = $this->createTweet($me, '自分のツイート');
        $other = $this->userAssistance->createUsers(1)->first();
        $tweets = $this->createTweets($other, 3);
        foreach ($tweets as $reply) {
            $this->createReplies($reply, $ownTweet);
        }

        // 実行
        $response = $this->actingAs($me)->get("api/tweets/{$ownTweet->id->value()}/replies");

        // 検証
        $response->assertStatus(200);
        $content = $response->json();
        $this->assertSame(
            [
                'replies' => [],
            ],
            $content
        );
        // TODO: データの確認もする

    }

    private function createTweet(TubuyakiUser $user, string $content): Tweet
    {
        $tweet = new Tweet(new Unidentified(), $user->id->value(), $content);
        return $this->tweetRepository->save($tweet);
    }

    /**
     * @return Collection<Tweet>
     */
    private function createTweets(TubuyakiUser $user, int $count): Collection
    {
        $tweets = collect([]);
        for ($i = 0; $i < $count; $i++) {
            $text = fake()->realText(140);
            $t = $this->createTweet($user, $text);
            $tweets->put($t->id->value(), $t);
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
