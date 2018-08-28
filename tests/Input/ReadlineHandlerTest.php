<?php

namespace Hugga\Test\Input;

use Hugga\Console;
use Hugga\Input\ReadlineHandler;
use Hugga\Test\TestCase;
use Mockery as m;

class ReadlineHandlerTest extends TestCase
{
    /** @var ReadlineHandler|m\Mock */
    protected $readline;

    protected function setUp()
    {
        parent::setUp();
        $this->readline = m::mock(ReadlineHandler::class, [$this->stdin])->makePartial();
        $this->readline->shouldAllowMockingProtectedMethods();
        $this->readline->shouldNotReceive('phpReadline')->byDefault();
    }

    /** @test */
    public function requiresAResource()
    {
        self::assertFalse(ReadlineHandler::isCompatible('php://memory'));
    }

    /** @test */
    public function requiresATty()
    {
        self::assertFalse(ReadlineHandler::isCompatible($this->stdin));
    }

    /** @test */
    public function requiresStdin()
    {
        if (!Console::isTty(STDIN)) {
            $this->markTestSkipped('STDIN needs to be a tty for this test');
        }

        self::assertFalse(ReadlineHandler::isCompatible(STDOUT));
        self::assertTrue(ReadlineHandler::isCompatible(STDIN));
    }

    /** @test */
    public function readLineJustCallsReadline()
    {
        $this->readline->shouldReceive('phpReadline')->with('', '$ ')
            ->once()->andReturn('Hello World!');

        $result = $this->readline->readLine('$ ');

        self::assertSame('Hello World!', $result);
    }

    /** @test */
    public function usesEmptyPromptByDefault()
    {
        $this->readline->shouldReceive('phpReadline')->with('', " ")
            ->once()->andReturn('foo bar');

        $this->readline->readLine();
    }

    /** @test */
    public function readsOneCharacter()
    {
        $this->emulateReadChar('✔');

        $char = $this->readline->read();

        self::assertSame('✔', $char);
    }

    /** @test */
    public function readsOverLineBreaks()
    {
        $this->emulateReadChar('foo' . PHP_EOL . 'bar');

        $result = $this->readline->read(7);

        self::assertSame('foo' . PHP_EOL . 'bar', $result);
    }

    /** @test */
    public function stopsOnFirstOccurance()
    {
        $this->emulateReadChar(
            'this is a long text with line breaks' . PHP_EOL .
            '. dot a the beginning' . PHP_EOL .
            'dot at the end .' . PHP_EOL .
            '.' . PHP_EOL
        );

        $result = $this->readline->readUntil(PHP_EOL . '.' . PHP_EOL);

        self::assertSame(
            'this is a long text with line breaks' . PHP_EOL .
            '. dot a the beginning' . PHP_EOL .
            'dot at the end .',
            $result
        );
    }

    protected function emulateReadChar(string $input)
    {
        $pos = 0;
        $closure = null;
        $lineBuffer = '';

        // prepare stdin stream to pass stream_select
        fwrite($this->stdin, $input);
        fseek($this->stdin, $pos);

        // store the closure
        $this->readline->shouldReceive('phpReadline')
            ->with('callback_handler_install', " ", m::type(\Closure::class))
            ->andReturnUsing(function ($m, $p, $c) use (&$closure, &$lineBuffer) {
                $lineBuffer = '';
                $closure = $c;
            })
            ->once()->ordered();

        // emulate reading the next char
        $this->readline->shouldReceive('phpReadline')->with('callback_read_char')
            ->andReturnUsing(function () use (&$lineBuffer, &$closure, &$pos, $input) {
                // get the next char
                $c = mb_substr($input, $pos, 1);

                if (is_callable($closure) && $c === "\n") {
                    // call closure on "\n" and reset the line buffer
                    $closure($lineBuffer);
                    $lineBuffer = '';
                } else {
                    // append char to line buffer
                    $lineBuffer .= $c;
                }

                // seek to next char
                $pos++;
                fseek($this->stdin, $pos);
            })
            ->times(mb_strlen($input))->ordered('loop');

        $this->readline->shouldReceive('phpReadline')->with('info', 'line_buffer')
            ->andReturnUsing(function () use (&$lineBuffer) {
                return $lineBuffer;
            })
            ->times(mb_strlen($input))->ordered('loop');

        $this->readline->shouldReceive('phpReadline')->with('callback_handler_remove')
            ->andReturnUsing(function () use (&$closure) {
                $closure = null;
            })
            ->once()->ordered();
    }
}
