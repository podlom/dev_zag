<?php

namespace Aimix\Gallery\Tests;

use Aimix\Gallery\Facades\Gallery;
use Aimix\Gallery\ServiceProvider;
use Orchestra\Testbench\TestCase;

class GalleryTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'gallery' => Gallery::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
