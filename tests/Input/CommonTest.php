<?php

namespace Hugga\Test\Input;

use Hugga\Input\File;
use Hugga\Test\TestCase;

class CommonTest extends TestCase
{
    /** @test */
    public function throwsWithoutResource()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('has to be of the type resource');

        new File($this->console, 'php://memory');
    }
}
