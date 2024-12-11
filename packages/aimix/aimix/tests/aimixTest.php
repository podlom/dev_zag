<?php

namespace Aimix\aimix\Tests;

use Aimix\aimix\Facades\aimix;
use Aimix\aimix\ServiceProvider;
use Orchestra\Testbench\TestCase;

class aimixTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'aimix' => aimix::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
