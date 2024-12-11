<?php

namespace aimix\account\Tests;

use aimix\account\Facades\account;
use aimix\account\ServiceProvider;
use Orchestra\Testbench\TestCase;

class accountTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'account' => account::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
