<?php

namespace aimix\account\Facades;

use Illuminate\Support\Facades\Facade;

class account extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'account';
    }
}
