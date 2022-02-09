<?php

namespace RedSnapper\Builder\Facades;

use Illuminate\Support\Facades\Facade;

class BuilderApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'builderApi';
    }
}