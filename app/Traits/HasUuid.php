<?php

namespace App\Traits;

trait HasUuid
{
    public static function uuid(string $uuid) : ?self
    {
        return self::where('uuid', '=', $uuid)->first();
    }
}
