<?php

namespace Hugga\Test\Output;

use Hugga\Output\File;
use Hugga\Test\TestCase;

class ResourceHandlerTest extends TestCase
{
    /** @test */
    public function doesNotRequireATty()
    {
        self::assertTrue(File::isCompatible($this->stdout));
    }

    /** @test */
    public function requiresWriteableResource()
    {
        self::assertFalse(File::isCompatible(fopen('php://memory', 'r')));
    }

    /** @test */
    public function writesToResource()
    {
        $handler = new File($this->console, $this->stdout);

        $handler->write('any string');

        rewind($this->stdout);
        self::assertSame('any string', fread($this->stdout, 4096));
    }

    /** @test */
    public function deletesTheLastBytes()
    {
        $handler = new File($this->console, $this->stdout);
        $handler->write('Calculating something ... ⚬');

        $handler->delete(1);

        rewind($this->stdout);
        self::assertSame('Calculating something ... ', fread($this->stdout, 4096));
    }

    /** @test */
    public function deletesTheCurrentLine()
    {
        $handler = new File($this->console, $this->stdout);
        $handler->write('Text before the last line' . PHP_EOL . 'text to be removed...');

        $handler->deleteLine();

        rewind($this->stdout);
        self::assertSame('Text before the last line' . PHP_EOL, fread($this->stdout, 4096));
    }

    /** @test */
    public function deletesInChunks()
    {
        $handler = new File($this->console, $this->stdout);
        $handler->write('Text before the last line' . PHP_EOL . 'foo bar');

        $handler->deleteLine(); // buffer size 3 -> 3 steps

        rewind($this->stdout);
        self::assertSame('Text before the last line' . PHP_EOL, fread($this->stdout, 4096));
    }

    /** @test */
    public function deletesEverythingWithoutLinebreak()
    {
        $handler = new File($this->console, $this->stdout);
        $handler->write('the first line of the file without line breaks');

        $handler->deleteLine();

        rewind($this->stdout);
        self::assertSame('', fread($this->stdout, 4096));
    }

    /** @test */
    public function deleteIsDisabledWhenResourceIsNotSeekable()
    {
        $handler = new File($this->console, $this->stdout);
        // mock not seekable as it is not possible to create a resource that is not seekable
        $this->setProtectedProperty($handler, 'seekable', false);

        $handler->write('Doing something ... in progress');
        $handler->delete(11);

        rewind($this->stdout);
        self::assertSame('Doing something ... in progress', fread($this->stdout, 4096));
    }

    /** @test */
    public function deleteLineIsDisabledWhenResourceIsNotSeekable()
    {
        $handler = new File($this->console, $this->stdout);
        // mock not seekable as it is not possible to create a resource that is not seekable
        $this->setProtectedProperty($handler, 'seekable', false);

        $handler->write('Text before the last line' . PHP_EOL . 'text to be removed...');
        $handler->deleteLine();

        rewind($this->stdout);
        self::assertSame('Text before the last line' . PHP_EOL . 'text to be removed...', fread($this->stdout, 4096));
    }
}
