<?php

namespace App\Providers;

use App\Repositories\User\ElqUserRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * 登録する必要のある全コンテナ結合
     *
     * @var array
     */
    public $bindings = [
        UserRepository::class => ElqUserRepository::class,
    ];

    /**
     * 登録する必要のある全コンテナシングルトン
     *
     * @var array
     */
    public $singletons = [
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
