<?php

use Illuminate\Support\Facades\Cache;
use Maize\Nps\Models\Nps;
use Maize\Nps\Models\NpsAnswer;
use Maize\Nps\Tests\Models\User;

test('can answer a nps', function (User $user, Nps $nps) {
    actingAs($user, 'api')
        ->postJson(routeByPartialName('nps.answer', $nps->getKey()), [
            'value' => '5',
            'answer' => 'test',
        ])
        ->assertStatus(204);

    assertModelHas(NpsAnswer::class, [
        'user_id' => $user->getKey(),
        'nps_id' => $nps->getKey(),
        'value' => '5',
        'answer' => 'test',
    ]);
})->with('user', 'current_nps');

test('can answer a nps with empty answer field', function (User $user, Nps $nps) {
    actingAs($user, 'api')
        ->postJson(routeByPartialName('nps.answer', $nps->getKey()), [
            'value' => '5',
            'answer' => null,
        ])
        ->assertStatus(204);

    assertModelHas(NpsAnswer::class, [
        'user_id' => $user->getKey(),
        'nps_id' => $nps->getKey(),
        'value' => '5',
        'answer' => null,
    ]);
})->with('user', 'current_nps');

test('can decline a nps', function (User $user, Nps $nps) {
    actingAs($user, 'api')
        ->postJson(routeByPartialName('nps.answer', $nps->getKey()), [
            'value' => null,
            'answer' => null,
        ])
        ->assertStatus(204);

    assertModelHas(NpsAnswer::class, [
        'user_id' => $user->getKey(),
        'nps_id' => $nps->getKey(),
        'value' => null,
        'answer' => null,
    ]);
})->with('user', 'current_nps');

test('cannot answer to a nps multiple times', function (User $user, Nps $nps) {
    actingAs($user, 'api')
        ->postJson(routeByPartialName('nps.answer', $nps->getKey()), [
            'value' => '5',
            'answer' => 'test',
        ])
        ->assertStatus(204);

    actingAs($user, 'api')
        ->postJson(routeByPartialName('nps.answer', $nps->getKey()), [
            'value' => '3',
            'answer' => 'test-test',
        ])
        ->assertStatus(204);

    assertModelHas(NpsAnswer::class, [
        'user_id' => $user->getKey(),
        'nps_id' => $nps->getKey(),
        'value' => '5',
        'answer' => 'test',
    ]);
})->with('user', 'current_nps');

test('cannot answer with invalid value', function (User $user, Nps $nps) {
    actingAs($user, 'api')
        ->postJson(routeByPartialName('nps.answer', $nps->getKey()), [
            'value' => '-1',
            'answer' => 'test',
        ])
        ->assertStatus(422);

    actingAs($user, 'api')
        ->postJson(routeByPartialName('nps.answer', $nps->getKey()), [
            'value' => '20',
            'answer' => 'test',
        ])
        ->assertStatus(422);

    actingAs($user, 'api')
        ->postJson(routeByPartialName('nps.answer', $nps->getKey()), [
            'value' => null,
            'answer' => 'test',
        ])
        ->assertStatus(422);

    assertModelMissing(NpsAnswer::class, [
        'user_id' => $user->getKey(),
        'nps_id' => $nps->getKey(),
    ]);
})->with('user', 'current_nps');

test('can delay nps answer', function (User $user, Nps $nps) {
    actingAs($user, 'api')
        ->postJson(routeByPartialName('nps.delay', $nps->getKey()))
        ->assertStatus(204);

    expect(
        $user->hasAnsweredCurrentNps()
    )->toBeTrue();

    assertModelMissing(NpsAnswer::class, [
        'user_id' => $user->getKey(),
        'nps_id' => $nps->getKey(),
    ]);
})->with('user', 'current_nps');

test('nps answer delay should be cached', function (User $user, Nps $nps) {
    actingAs($user, 'api')
        ->postJson(routeByPartialName('nps.delay', $nps->getKey()))
        ->assertStatus(204);

    expect(
        Cache::has(NpsAnswer::npsAnswerCacheKey($nps, $user))
    )->toBeTrue();

    expect(
        Cache::get(NpsAnswer::npsAnswerCacheKey($nps, $user))
    )->toBe(true);
})->with('user', 'current_nps');

test('nps answer check should be cached', function (User $user, Nps $nps) {
    actingAs($user, 'api')
        ->postJson(routeByPartialName('nps.answer', $nps->getKey()), [
            'value' => 5,
            'answer' => 'test',
        ])
        ->assertStatus(204);

    expect(
        Cache::has(NpsAnswer::npsAnswerCacheKey($nps, $user))
    )->toBeTrue();

    expect(
        Cache::get(NpsAnswer::npsAnswerCacheKey($nps, $user))
    )->toBe(true);
})->with('user', 'current_nps');
