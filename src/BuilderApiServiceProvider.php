<?php

namespace RedSnapper\Builder;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class BuilderApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'builder-api');

        $this->app->singleton(BuilderClient::class, function (Application $app) {
            return new BuilderClient(
                config('builder-api.site'),
                config('builder-api.user'),
                config('builder-api.password'),
                config('builder-api.preview'),
            );
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('builder-api.php'),
            ], 'config');
        }
    }
}