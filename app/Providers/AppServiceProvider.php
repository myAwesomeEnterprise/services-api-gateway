<?php

namespace App\Providers;

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
        $this->app->singleton(RouteRegistrationService::class, function ($app) {
            return new RouteRegistrationService($app->router);
        });

        $this->app->singleton(Client::class, function() {
            return new Client([
                'debug' => config('gateway.http_client.debug'),
                'timeout' => config('gateway.http_client.timeout'),
                'connect_timeout' => config('gateway.http_client.connect_timeout', config('gateway.http_client.timeout')),
                'allow_redirects' => config('gateway.http_client.allow_redirects'),
            ]);
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
