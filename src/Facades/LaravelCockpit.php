<?php

namespace EntrieCloud\LaravelCockpit\Facades;

use EntrieCloud\LaravelCockpit\LaravelCockpit as BaseLaravelCockpit;
use EntrieCloud\LaravelCockpit\LaravelCockpitManager;
use Illuminate\Support\Facades\Facade;

/**
 * @see BaseLaravelCockpit
 * @see LaravelCockpitManager
 */
class LaravelCockpit extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-cockpit';
    }
}
