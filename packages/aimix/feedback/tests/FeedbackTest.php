<?php

namespace Aimix\Feedback\Tests;

use Aimix\Feedback\Facades\Feedback;
use Aimix\Feedback\ServiceProvider;
use Orchestra\Testbench\TestCase;

class FeedbackTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'feedback' => Feedback::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
