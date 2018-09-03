<?php

namespace Hugga\Test\Input;

use Hugga\Input\File as InputHandler;
use Hugga\Test\TestCase;

class ResourceHandlerTest extends TestCase
{
    /** @test */
    public function worksWithAnyResource()
    {
        self::assertTrue(InputHandler::isCompatible($this->stdin));
    }

    /** @test */
    public function readLineReadsTillEndOfLine()
    {
        fwrite($this->stdin, 'foo' . PHP_EOL . 'bar' . PHP_EOL);
        rewind($this->stdin);
        $handler = new InputHandler($this->console, $this->stdin);

        self::assertSame('foo', $handler->readLine());
        self::assertSame('bar', $handler->readLine());
    }

    /** @test */
    public function readsOneByte()
    {
        fwrite($this->stdin, '✔✘');
        rewind($this->stdin);
        $handler = new InputHandler($this->console, $this->stdin);

        self::assertSame('✔', $handler->read());
        self::assertSame('✘', $handler->read());
    }

    /** @test */
    public function readsFileTillFirstOccurance()
    {
        fwrite(
            $this->stdin,
            'this could be a long text' . PHP_EOL . '.' . PHP_EOL .
            'this remains in the file' . PHP_EOL
        );
        rewind($this->stdin);
        $handler = new InputHandler($this->console, $this->stdin);

        self::assertSame('this could be a long text', $handler->readUntil(PHP_EOL . '.' . PHP_EOL));
        self::assertSame('this remains in the file', $handler->readLine());
    }

    /** @test */
    public function readsTillEndOfFile()
    {
        fwrite($this->stdin, 'the requested end does not exist');
        rewind($this->stdin);
        $handler = new InputHandler($this->console, $this->stdin);

        self::assertSame('the requested end does not exist', $handler->readUntil(PHP_EOL . PHP_EOL));
    }
}
