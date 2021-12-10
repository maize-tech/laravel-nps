<?php

use Maize\Nps\Models\Nps;

dataset('current_nps', function () {
    yield fn () => Nps::factory()->create([
        'starts_at' => now()->subDays(2),
        'ends_at' => now()->addDays(2),
    ]);
});
