<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nps model
    |--------------------------------------------------------------------------
    |
    | Here you may specify the fully qualified class name of the nps model class.
    |
    */

    'nps_model' => Maize\Nps\Models\Nps::class,

    /*
    |--------------------------------------------------------------------------
    | Nps answer model
    |--------------------------------------------------------------------------
    |
    | Here you may specify the fully qualified class name of the nps answer
    | model class.
    |
    */

    'nps_answer_model' => Maize\Nps\Models\NpsAnswer::class,

    /*
    |--------------------------------------------------------------------------
    | Nps finder
    |--------------------------------------------------------------------------
    |
    | Here you may specify the fully qualified class name of the nps finder class.
    |
    */

    'nps_finder' => Maize\Nps\DefaultNpsFinder::class,

    /*
    |--------------------------------------------------------------------------
    | Nps visibility
    |--------------------------------------------------------------------------
    |
    | Here you may associate a custom visibility class to a name, which is then
    | used as a reference in the nps model.
    |
    */

    'visibility' => [
        'default' => Maize\Nps\DefaultNpsVisibility::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Nps range
    |--------------------------------------------------------------------------
    |
    | Here you may associate a custom range class to a name, which is then
    | used as a reference in the nps model.
    |
    */

    'range' => [
        'default' => Maize\Nps\DefaultNpsRange::class,
        'minimal' => Maize\Nps\MinimalNpsRange::class,
        'emoji' => Maize\Nps\EmojiNpsRange::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Route configurations
    |--------------------------------------------------------------------------
    |
    | Here you may specify whether routes should be enabled or not.
    | You can also customize the routes prefix and middlewares.
    |
    */

    'routes' => [
        'enabled' => true,
        'prefix' => '',
        'name' => 'nps',
        'middleware' => ['auth:api'],
        'endpoints' => [
            'show' => [
                'middleware' => [],
            ],
            'answer' => [
                'middleware' => [],
            ],
            'delay' => [
                'middleware' => [],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Here you may specify the amount of time, in seconds, where each nps
    | is cached to avoid multiple database queries.
    |
    */

    'cache' => [
        'nps_ttl' => 3600,
        'nps_answer_ttl' => 3600,
    ],
];
