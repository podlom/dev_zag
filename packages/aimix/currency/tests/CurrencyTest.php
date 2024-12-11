<?php

namespace Aimix\Currency\Tests;

use Aimix\Currency\Facades\Currency;
use Aimix\Currency\ServiceProvider;
use Orchestra\Testbench\TestCase;

class CurrencyTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'currency' => Currency::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
