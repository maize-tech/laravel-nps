<?php

namespace Maize\Nps;

abstract class NpsRange
{
    protected static array $values = [];

    public static function toArray(): array
    {
        return static::$values ?? [];
    }
}
