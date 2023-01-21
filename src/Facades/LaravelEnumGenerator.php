<?php

declare(strict_types=1);

namespace HaakCo\LaravelEnumGenerator\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelEnumGenerator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravelenumgenerator';
    }
}
