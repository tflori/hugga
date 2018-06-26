<?php

namespace Hugga\Test\Console;

use Hugga\Test\TestCase;

class WriteTest extends TestCase
{
    /** @test */
    public function writesByDefault()
    {
        $this->console->write('Hello World!');

        rewind($this->stdout);
        self::assertSame('Hello World!', fread($this->stdout, 4096));
    }
}
