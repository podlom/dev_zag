<?php

namespace Aimix\Banner\Facades;

use Illuminate\Support\Facades\Facade;

class Banner extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'banner';
    }
}
