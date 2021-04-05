<?php

namespace Zhineng\Checkpoint\Tencent;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CheckpointServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/checkpoint.php', 'checkpoint'
        );
    }

    public function boot()
    {
        $this->bootRoutes();
        $this->bootMigrations();
    }

    protected function bootRoutes(): void
    {
        if (! Checkpoint::$registersRoutes) {
            return;
        }

        Route::group([
            'prefix' => config('checkpoint.path'),
            'as' => 'checkpoint',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    protected function bootMigrations(): void
    {
        if (Checkpoint::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
