<?php

namespace Zhineng\Checkpoint\Tencent\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Zhineng\Checkpoint\Tencent\CheckpointServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [CheckpointServiceProvider::class];
    }
}