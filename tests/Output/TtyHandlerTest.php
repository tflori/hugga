<?php

namespace Hugga\Test\Output;

use Hugga\Output\TtyHandler;
use Hugga\Test\TestCase;

class TtyHandlerTest extends TestCase
{
    /** @test */
    public function requiresATty()
    {
        self::assertFalse(TtyHandler::isCompatible($this->stdout));
    }

    /** @test */
    public function writesToResource()
    {
        $handler = new TtyHandler($this->stdout);

        $handler->write('any string');

        rewind($this->stdout);
        self::assertSame('any string', fread($this->stdout, 4096));
    }

    /** @test */
    public function outputsCursorMovement()
    {
        $handler = new TtyHandler($this->stdout);

        $handler->delete(1);

        rewind($this->stdout);
        self::assertSame("\e[D \e[D", fread($this->stdout, 4096));
    }

    /** @test */
    public function outputsCarriageReturnAndDeleteLine()
    {
        $handler = new TtyHandler($this->stdout);

        $handler->deleteLine();

        rewind($this->stdout);
        self::assertSame("\e[1K\r", fread($this->stdout, 4096));
    }
}
