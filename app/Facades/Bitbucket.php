<?php

namespace Mtvtd\Deploy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \Mtvtd\Deploy\Support\Bitbucket
 */
class Bitbucket extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mtvtd\Deploy\Support\Bitbucket::class;
    }
}
