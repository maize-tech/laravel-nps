<?php

namespace Maize\Nps\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maize\Nps\NpsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Maize\\Nps\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            NpsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('auth.guards', [
            'web' => [
                'driver' => 'session',
                'provider' => 'users',
            ],
            'api' => [
                'driver' => 'token',
                'provider' => 'users',
                'hash' => false,
            ],
        ]);

        $migration = include __DIR__.'/../database/migrations/create_users_table.php.stub';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/create_nps_tables.php.stub';
        $migration->up();
    }
}
