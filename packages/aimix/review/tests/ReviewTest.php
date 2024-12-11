<?php

namespace Aimix\Review\Tests;

use Aimix\Review\Facades\Review;
use Aimix\Review\ServiceProvider;
use Orchestra\Testbench\TestCase;

class ReviewTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'review' => Review::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
