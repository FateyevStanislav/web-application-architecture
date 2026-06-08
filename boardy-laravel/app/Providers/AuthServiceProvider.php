<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\Contracts\AuthorizationViewResponse as AuthorizationViewResponseContract;
use Laravel\Passport\Http\Responses\SimpleViewResponse;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Post' => 'App\Policies\PostPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::tokensExpireIn(now()->addMinutes(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::enablePasswordGrant();

        $this->app->bind(
            AuthorizationViewResponseContract::class,
            fn () => new SimpleViewResponse('oauth.authorize')
        );
    }
}
