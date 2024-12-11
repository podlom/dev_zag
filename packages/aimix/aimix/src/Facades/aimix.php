<?php

namespace Aimix\aimix\Facades;

use Illuminate\Support\Facades\Facade;

class aimix extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'aimix';
    }
}
