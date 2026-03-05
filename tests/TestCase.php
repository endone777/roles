<?php

namespace Endone777\Roles\Tests;

use Endone777\Roles\RolesServiceProvider;
use Orchestra\Testbench\TestCase as TestBenchTestCase;

abstract class TestCase extends TestBenchTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [RolesServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function runMigrations(): void
    {
        $this->loadMigrationsFrom([
            realpath(__DIR__ . '/../migrations'),
            realpath(__DIR__ . '/database/migrations'),
        ]);
    }
}
