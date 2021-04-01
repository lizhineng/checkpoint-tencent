<?php

namespace Zhineng\Checkpoint\Tencent\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Zhineng\Checkpoint\Tencent\CheckpointServiceProvider;
use Zhineng\Checkpoint\Tencent\Tests\Fixtures\User;

class FeatureTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();

        $this->artisan('migrate')->run();
    }

    protected function createIdentifiable($description = 'zhineng', array $options = []): User
    {
        return User::create(array_merge([
            'email' => $description.'@checkpoint.test',
            'name' => 'Li Zhineng',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ], $options));
    }

    protected function getPackageProviders($app)
    {
        return [CheckpointServiceProvider::class];
    }
}