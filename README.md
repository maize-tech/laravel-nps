<p align="center"><img src="/art/socialcard.png" alt="Social Card of Laravel NPS"></p>

# Laravel NPS

[![Latest Version on Packagist](https://img.shields.io/packagist/v/maize-tech/laravel-nps.svg?style=flat-square)](https://packagist.org/packages/maize-tech/laravel-nps)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/maize-tech/laravel-nps/run-tests?label=tests)](https://github.com/maize-tech/laravel-nps/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/maize-tech/laravel-nps/Check%20&%20fix%20styling?label=code%20style)](https://github.com/maize-tech/laravel-nps/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/maize-tech/laravel-nps.svg?style=flat-square)](https://packagist.org/packages/maize-tech/laravel-nps)

Easily integrate custom-made NPS (Net Promoter Score) to your application.

## Installation

You can install the package via composer:

```bash
composer require maize-tech/laravel-nps
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="nps-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --tag="nps-config"
```

This is the contents of the published config file:

```php
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
```

## Usage

### Basic

To use the package, add the `Maize\Nps\CanAnswerNps` trait to the User model.

Here's an example model including the `CanAnswerNps` trait:

``` php
<?php

namespace App\Models;

use Maize\Nps\CanAnswerNps;

class User extends Model
{
    use CanAnswerNps;

    protected $fillable = [
        'fist_name',
        'last_name',
        'email',
    ];
}
```

You can then create one or multiple NPS from the DB or, if you wish, you could handle the creation with a CMS.

Here are the fields who should be filled:
- **question**: the question that identifies the NPS
- **starts_at**: the start date of the NPS
- **ends_at**: the end date of the NPS
- **range**: the value ranges accepted for the given NPS. This value must be equal to one of the keys defined in the `range` list from `config/nps.php`. Defaults to `default`.
- **visibility**: the visibility for the given NPS. This value must be equal to one of the keys defined in the `visibility` list from `config/nps.php`. Defaults to `default`.

Let's say we create a NPS which starts on 2021-01-01 and ends after a week: here's the model entity we would have:

``` php
$nps = [
    "id" => 1,
    "question" => "How would you rate our platform?",
    "starts_at" => "2021-01-01",
    "ends_at" => "2021-01-31",
    "range" => "default", // default range is 1-5
    "visibility" => "default", // by default a NPS is always visible
    "updated_at" => "2021-01-01",
    "created_at" => "2021-01-01",
];
```

You can now call the custom API to retrieve, reply or delay the current NPS, which can be customized in `config/nps.php`:

#### GET - **/nps**

This endpoint retrieves the current NPS using the given criteria:
- the `starts_at` date must be earlier than `now()`
- the `ends_at` date must be older than `now()`
- the NPS must be visible (defaults to `true`)

The NPS entries are then filtered by their ends_at date in order to pick the first one expiring.

The response contains the NPS id (used for the POST route), the question, and the accepted values.
Here is a sample response body:

``` json
{
    "data": {
        "id": 1,
        "values": [
            1,
            2,
            3,
            4,
            5
        ],
        "question": "How would you rate our platform?"
    }
}
```

#### POST - **/nps/{id}**

This endpoint stores the answer for the given NPS from the currently authenticated user. The request requires the following attributes:
- **value**: the value chose by the user
- **answer**: the (optional) answer written by the user

There are some basic validation rules applied to the request:
- the **value** attribute must be an integer
- the **value** attribute should be between the range defined in the given NPS model
- a user should not be able to submit the answer if **value** is `null`
- a user should be able to decline their submission for the NPS. In this case, both **value** and **answer** attributes should be `null`

#### POST - **/nps/{id}/delay**

This endpoint marks the NPS answered in cache for the currently authenticated user, but only for a limited time.

This way, the `hasAnsweredCurrentNps` and `hasAnsweredNps` methods will return `true` even if the answer is not stored in the database.

The user will then be able to see again the NPS only after that amount of time has expired.

The amount of time (in seconds) can be configured in the `nps_answer_ttl` attribute from `config/nps.php`.

### Custom range class

If you wish to define a custom range of values for a NPS, you can define a new class which extends the NpsRange abstract class and contains a `$values` array.

``` php
class MyCustomNpsRange extends NpsRange
{
    protected static array $values = [2, 4, 6, 8, 10];
}
```

You can then associate the class with a name used as identified for the `range` attribute of the NPS model.
This can be easily done by adding the key-value pair in the `range` list from `config/nps.php`:

``` php
'range' => [
    'default' => Maize\Nps\DefaultNpsRange::class,
    'minimal' => Maize\Nps\MinimalNpsRange::class,
    'emoji' => Maize\Nps\EmojiNpsRange::class,
    'custom' => Path\To\MyCustomNpsRange::class,
]
```

You can also define an associative array in case you want the values to be an array of strings.
In this case, the key should contain the string, whereas the value is the associated integer which should be sent from the POST endpoint.

``` php
class EmojiNpsRange extends NpsRange
{
    protected static array $values = [
        '😡' => 1,
        '🙁' => 2,
        '😐' => 3,
        '😉' => 4,
        '😍' => 5,
    ];
}

```

### Custom visibility class

If you wish to define a custom visibility for a NPS, you can define a new class which extends the NpsVisibility abstract class and implement the `__invoke` abstract method.

For example, let's say we want a NPS to be visible for a user when they performed at least 5 logins.
Here is the custom visibility class we should have:

``` php
class MinLoginsVisibility extends NpsVisibility
{
    public function __invoke(Nps $nps): bool
    {
        return auth()->user()->accessLogs()->count() >= 5;
    }
}
```

You can then associate the class with a name used as identified for the `visibility` attribute of the NPS model.
This can be easily done by adding the key-value pair in the `visibility` list from `config/nps.php`:

``` php
'visibility' => [
    'default' => Maize\Nps\DefaultNpsVisibility::class,
    'min_logins' => Path\To\MinLoginsVisibility::class,
]
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Enrico De Lazzari](https://github.com/enricodelazzari)
- [Riccardo Dalla Via](https://github.com/riccardodallavia)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
