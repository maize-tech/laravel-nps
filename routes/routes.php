<?php

use Maize\Nps\Http\Controllers\NpsAnswerDelayController;
use Maize\Nps\Http\Controllers\NpsController;
use Maize\Nps\Http\Controllers\NpsAnswerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

if (config('nps.routes.enabled')) {
    $prefix = config('nps.routes.prefix');
    $middleware = config('nps.routes.middleware');
    $name = config('nps.routes.name');

    Route::group([
        'prefix' => $prefix,
        'as' => Str::finish($name, '.'),
        'middleware' => $middleware,
    ], function () {

        Route::get('nps', NpsController::class)
            ->name('show')
            ->middleware(
                config('nps.routes.endpoints.show.middleware')
            );

        Route::post('nps/{id}', NpsAnswerController::class)
            ->name('answer')
            ->middleware(
                config('nps.routes.endpoints.answer.middleware')
            );

        Route::post('nps/{id}/delay', NpsAnswerDelayController::class)
            ->name('delay')
            ->middleware(
                config('nps.routes.endpoints.delay.middleware')
            );
    });
}
