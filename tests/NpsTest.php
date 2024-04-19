<?php

use Illuminate\Support\Facades\Cache;
use Maize\Nps\Models\Nps;
use Maize\Nps\Tests\Models\User;

test('can get a nps', function () {
    $user = User::factory()->create();

    $nps = Nps::factory()->create([
        'starts_at' => now()->subDays(2),
        'ends_at' => now()->addDays(2),
    ]);

    actingAs($user, 'api')
        ->getJson(routeByPartialName('nps.show'))
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $nps->getKey(),
                'question' => $nps->question,
            ],
        ]);
});

test('cannot get a ended nps', function () {

    $user = User::factory()->create();

    Nps::factory()->create([
        'starts_at' => now()->subDays(4),
        'ends_at' => now()->subDays(2),
    ]);

    actingAs($user, 'api')
        ->getJson(routeByPartialName('nps.show'))
        ->assertStatus(404);
});

test('cannot get a not started nps', function () {
    $user = User::factory()->create();

    Nps::factory()->create([
        'starts_at' => now()->addDays(4),
        'ends_at' => now()->addDays(2),
    ]);

    actingAs($user, 'api')
        ->getJson(routeByPartialName('nps.show'))
        ->assertStatus(404);
});

test('can get current nps', function (User $user, Nps $nps) {
    Nps::factory()->create([
        'starts_at' => null,
        'ends_at' => null,
    ]);

    Nps::factory()->create([
        'starts_at' => null,
        'ends_at' => now()->subDays(10),
    ]);

    Nps::factory()->create([
        'starts_at' => now()->subDays(10),
        'ends_at' => null,
    ]);

    Nps::factory()->create([
        'starts_at' => now()->subDays(10),
        'ends_at' => now()->subDays(2),
    ]);

    Nps::factory()->create([
        'starts_at' => now()->subDays(8),
        'ends_at' => now()->subDays(4),
    ]);

    Nps::factory()->create([
        'starts_at' => now()->addDays(20),
        'ends_at' => now()->addDays(25),
    ]);

    actingAs($user, 'api')
        ->getJson(routeByPartialName('nps.show'))
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $nps->getKey(),
            ],
        ]);
})->with('user', 'current_nps');

test('nps should be cached', function (User $user, Nps $nps) {
    actingAs($user, 'api')
        ->getJson(routeByPartialName('nps.show'))
        ->assertStatus(200);

    expect(
        Cache::has(Nps::npsCacheKey())
    )->toBeTrue();

    expect(
        Cache::get(Nps::npsCacheKey())->toArray()
    )->toMatchArray($nps->toArray());
})->with('user', 'current_nps');
