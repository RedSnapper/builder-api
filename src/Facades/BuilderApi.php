<?php

namespace RedSnapper\Builder\Facades;

use Illuminate\Support\Facades\Facade;
use RedSnapper\Builder\BuilderResponse;
use RedSnapper\Builder\PendingRequest;

/**
 * @method static PendingRequest new()
 * @method static BuilderResponse get(string $macro, array $params = [])
 */
class BuilderApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'builderApi';
    }
}