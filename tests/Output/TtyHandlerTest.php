<?php

namespace Hugga\Test\Output;

use Hugga\Output\Tty;
use Hugga\Test\TestCase;

class TtyHandlerTest extends TestCase
{
    /** @test */
    public function requiresATty()
    {
        self::assertFalse(Tty::isCompatible($this->stdout));
    }

    /** @test */
    public function writesToResource()
    {
        $handler = new Tty($this->console, $this->stdout);

        $handler->write('any string');

        rewind($this->stdout);
        self::assertSame('any string', fread($this->stdout, 4096));
    }

    /** @test */
    public function outputsCursorMovement()
    {
        $handler = new Tty($this->console, $this->stdout);

        $handler->delete(1);

        rewind($this->stdout);
        self::assertSame("\e[D \e[D", fread($this->stdout, 4096));
    }

    /** @test */
    public function outputsCarriageReturnAndDeleteLine()
    {
        $handler = new Tty($this->console, $this->stdout);

        $handler->deleteLine();

        rewind($this->stdout);
        self::assertSame("\e[2K\r", fread($this->stdout, 4096));
    }
}
