<?php

namespace Zhineng\Checkpoint\Tencent;

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
        //
    }
}