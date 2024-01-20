<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Auth\TubuyakiUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //カスタムプロバイダの名前を定義
        \Auth::provider(
            //この部分の名前は何でもよい。config/auth.php には、この名称で設定を行う。
            'tubuyaki_user',
            function ($app, array $config) {
                return new TubuyakiUserProvider($app['hash']);
            }
        );
    }
}
