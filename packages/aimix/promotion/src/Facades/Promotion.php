<?php

namespace Aimix\Promotion\Facades;

use Illuminate\Support\Facades\Facade;

class Promotion extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'promotion';
    }
}
