<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \App\Support\Config
 */
class Config extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Support\Config::class;
    }
}
