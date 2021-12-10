<?php

use Illuminate\Foundation\Auth\User as Authenticatable;
use Maize\Nps\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function actingAs(Authenticatable $user, string $driver = null): TestCase
{
    return test()->actingAs($user, $driver);
}

function assertModelHas(string $model, array $data): TestCase
{
    return test()->assertDatabaseHas(
        (new $model())->getTable(),
        $data
    );
}

function assertModelMissing(string $model, array $data): TestCase
{
    return test()->assertDatabaseMissing(
        (new $model())->getTable(),
        $data
    );
}

function routeByPartialName(string $name, ...$args): string
{
    $prefix = config('nps.routes.prefix');

    if (empty($prefix)) {
        return route("{$name}", ...$args);
    }

    return route("{$prefix}.{$name}", ...$args);
}
