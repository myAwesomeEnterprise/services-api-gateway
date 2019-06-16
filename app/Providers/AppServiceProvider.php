<?php

namespace App\Providers;

use App\Services\RequestDelegationService;
use App\Services\RouteRegistrationService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function() {
            return new Client(config('gateway.http_client'));
        });

        $this->app->singleton(RouteRegistrationService::class, function ($app) {
            return new RouteRegistrationService($app->router);
        });

        $this->app->singleton(RequestDelegationService::class, function ($app) {
            return new RequestDelegationService(app(Client::class));
        });
    }

    /**
     * @return void
     */
    public function boot()
    {
        app(RouteRegistrationService::class)->register(app()->request);
    }
}
