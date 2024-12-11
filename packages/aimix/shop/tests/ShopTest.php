<?php

namespace Aimix\Shop\Tests;

use Aimix\Shop\Facades\Shop;
use Aimix\Shop\ServiceProvider;
use Orchestra\Testbench\TestCase;

class ShopTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'shop' => Shop::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
