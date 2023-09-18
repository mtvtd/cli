<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \App\Support\Bitbucket
 */
class Bitbucket extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Support\Bitbucket::class;
    }
}
