<?php

namespace Aimix\Banner\Tests;

use Aimix\Banner\Facades\Banner;
use Aimix\Banner\ServiceProvider;
use Orchestra\Testbench\TestCase;

class BannerTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'banner' => Banner::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
