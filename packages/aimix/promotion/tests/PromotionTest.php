<?php

namespace Aimix\Promotion\Tests;

use Aimix\Promotion\Facades\Promotion;
use Aimix\Promotion\ServiceProvider;
use Orchestra\Testbench\TestCase;

class PromotionTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'promotion' => Promotion::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
