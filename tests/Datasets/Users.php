<?php

use Maize\Nps\Tests\Models\User;

dataset('user', function () {
    yield fn () => User::factory()->create();
});
