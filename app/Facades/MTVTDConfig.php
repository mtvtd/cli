<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \App\Support\MTVTDConfig
 */
class MTVTDConfig extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Support\MTVTDConfig::class;
    }
}
