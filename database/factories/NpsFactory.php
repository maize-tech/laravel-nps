<?php

namespace Maize\Nps\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Maize\Nps\Models\Nps;

class NpsFactory extends Factory
{
    protected $model = Nps::class;

    public function definition()
    {
        return [
            'question' => $this->faker->sentence(),
            'starts_at' => null,
            'ends_at' => null,
            'range' => 'default',
            'visibility' => 'default',
        ];
    }
}
