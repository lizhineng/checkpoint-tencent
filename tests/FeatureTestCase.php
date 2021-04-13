<?php

namespace Zhineng\Checkpoint\Tencent\Tests;

use Orchestra\Testbench\TestCase;

class FeatureTestCase extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('services.checkpoint', [
            'key' => 'foo',
            'secret' => 'bar',
        ]);
    }
}
