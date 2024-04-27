<?php

use App\Http\Controllers\Follow\FollowController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 認証後のAPI
Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'users'], function () {
        Route::group(['prefix' => 'me'], function () {
            Route::get('/', [UserController::class, 'me']);
            Route::get('/tweets', [TweetController::class, 'getMyTweets']);
            Route::get('/following', [FollowController::class, 'getMyFollowees']);
            Route::get('/followers', [FollowController::class, 'getMyFollowers']);
        });
        Route::group(['prefix' => '{id}'], function () {
            Route::group(['prefix' => 'following'], function () {
                Route::get('/', [FollowController::class, 'getFollowees']);
                Route::post('/', [FollowController::class, 'follow']);
                Route::delete('/', [FollowController::class, 'unfollow']);
            });
            Route::group(['prefix' => 'followers'], function () {
                Route::get('/', [FollowController::class, 'getFollowers']);
            });
            Route::get('/tweets', [TweetController::class, 'getTweets']);
            Route::get('/timeline', [TimelineController::class, 'getTimeline']);
        })->whereNumber('id');
    });
    Route::group(['prefix' => 'tweets'], function () {
        Route::post('/', [TweetController::class, 'post']);
        Route::group(['prefix' => '{id}'], function () {
            Route::group(['prefix' => 'replies'], function () {
                Route::get('/', [TweetController::class, 'getReplies']);
                Route::post('/', [TweetController::class, 'reply']);
            });
            Route::post('/retweet', [TweetController::class, 'retweet']);
        })->whereNumber('id');
    });
});

Route::post('/users', [UserController::class, 'store']);
