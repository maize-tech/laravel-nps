<?php

namespace Maize\Nps\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maize\Nps\DefaultNpsRange;
use Maize\Nps\DefaultNpsVisibility;

class Nps extends Model
{
    use HasFactory;

    protected $table = 'nps';

    protected $fillable = [
        'question',
        'starts_at',
        'ends_at',
        'range',
        'visibility',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public static function npsCacheKey(): string
    {
        return "nps.current";
    }

    public function isVisible(): bool
    {
        $visibility = config(
            "nps.visibility.{$this->visibility}", /** @phpstan-ignore-line */
            DefaultNpsVisibility::class
        );

        return app($visibility)($this);
    }

    public function getValuesAttribute(): array
    {
        $values = config(
            "nps.range.{$this->range}", /** @phpstan-ignore-line */
            DefaultNpsRange::class
        );

        return app($values)->toArray();
    }
}
