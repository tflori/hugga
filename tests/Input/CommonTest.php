<?php

namespace Hugga\Test\Input;

use Hugga\Input\FileHandler;
use Hugga\Test\TestCase;

class CommonTest extends TestCase
{
    /** @test */
    public function throwsWithoutResource()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('has to be of the type resource');

        new FileHandler('php://memory');
    }
}
