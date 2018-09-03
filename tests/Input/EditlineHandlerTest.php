<?php

namespace Hugga\Test\Input;

use Hugga\Console;
use Hugga\Input\Editline;
use Hugga\Input\Observer;
use Hugga\Test\TestCase;
use Mockery as m;

class EditlineHandlerTest extends TestCase
{
    /** @var Editline|m\Mock */
    protected $editline;

    /** @var \Hugga\Input\Observer|m\Mock */
    protected $inputObserver;

    protected function setUp()
    {
        parent::setUp();

        $this->editline = m::mock(Editline::class, [$this->console, $this->stdin])->makePartial();
        $this->editline->shouldAllowMockingProtectedMethods();
        $this->editline->shouldNotReceive('phpReadline')->byDefault();

        $this->inputObserver = m::mock(Observer::class, [$this->stdin])->makePartial();
        $this->inputObserver->shouldReceive('start')->byDefault();
        $this->console->shouldReceive('getInputObserver')->andReturn($this->inputObserver)->byDefault();
    }
    /** @test */
    public function requiresAResource()
    {
        self::assertFalse(Editline::isCompatible('php://memory'));
    }

    /** @test */
    public function requiresATty()
    {
        self::assertFalse(Editline::isCompatible($this->stdin));
    }

    /** @test */
    public function requiresStdin()
    {
        self::assertFalse(Editline::isCompatible(STDOUT));
    }

    /** @test */
    public function requiresEditline()
    {
        if (!Console::isTty(STDIN)) {
            $this->markTestSkipped('STDIN needs to be a tty for this test');
        }

        $expected = readline_info('library_version') === 'EditLine wrapper';

        self::assertSame($expected, Editline::isCompatible(STDIN));
    }

    /** @test */
    public function getsAndUsesInputObserver()
    {
        $this->console->shouldReceive('getInputObserver')->with()
            ->once()->andReturn($this->inputObserver);
        $this->inputObserver->shouldReceive('start')->with()
            ->once();

        $result = $this->editline->read(10);

        self::assertSame('', $result);
    }

    /** @test */
    public function registersHandlerForWriting()
    {
        /** @var \Closure $handler */
        $handler = null;
        $this->inputObserver->shouldReceive('addHandler')->with(m::type(\Closure::class))
            ->once()->andReturnUsing(function ($h) use (&$handler) {
                $handler = $h;
            });

        $this->inputObserver->shouldReceive('start')->with()
            ->once()->andReturnUsing(function () use (&$handler) {
                $event = (object)[
                    'type' => 'keyUp',
                    'char' => 'ö',
                    'stopPropagation' => false,
                ];
                $this->console->shouldReceive('write')->with('ö')->once();
                $handler($event);
            });

        $result = $this->editline->read(10);

        self::assertSame('ö', $result);
    }

    /** @test */
    public function theHandlerIgnoresNonPrintableCharactersExceptLineFeed()
    {
        /** @var \Closure $handler */
        $handler = null;
        $this->inputObserver->shouldReceive('addHandler')->with(m::type(\Closure::class))
            ->once()->andReturnUsing(function ($h) use (&$handler) {
                $handler = $h;
            });
        $this->inputObserver->shouldReceive('start')->with()
            ->once()->andReturnUsing(function () use (&$handler) {
                $this->console->shouldReceive('write')->with(PHP_EOL)->once();
                $handler((object)['char' => "\x07"]); // a bell
                $handler((object)['char' => PHP_EOL]);
            });

        $result = $this->editline->read(10);

        self::assertSame(PHP_EOL, $result);
    }

    /** @test */
    public function theHandlerStopsTheInput()
    {
        /** @var \Closure $handler */
        $handler = null;
        $this->inputObserver->shouldReceive('addHandler')->with(m::type(\Closure::class))
            ->once()->andReturnUsing(function ($h) use (&$handler) {
                $handler = $h;
            });
        $this->inputObserver->shouldReceive('start')->with()
            ->once()->andReturnUsing(function () use (&$handler) {
                $this->console->shouldReceive('write')->with('foo bar')->once();
                $this->inputObserver->shouldReceive('stop')->with()
                    ->once();
                $handler((object)['char' => 'foo bar']); // magically with 7 characters
            });

        $result = $this->editline->read(7);

        self::assertSame('foo bar', $result);
    }

    /** @test */
    public function registersAHandlerForBackspace()
    {
        /** @var \Closure $handler */
        $handler = null;
        $this->inputObserver->shouldReceive('on')->with("\x7f", m::type(\Closure::class))
            ->once()->andReturnUsing(function ($h) use (&$handler) {
                $handler = $h;
            });

        $result = $this->editline->read(10);

        self::assertSame('', $result);
    }

    /** @test */
    public function theHandlerDeletesACharacterFromResultAndStopsPropagation()
    {
        /** @var \Closure $handler */
        $handler = null;
        $this->inputObserver->shouldReceive('on')->with("\x7f", m::type(\Closure::class))
            ->once()->andReturnUsing(function ($char, $h) use (&$handler) {
                $handler = $h;
            });
        /** @var \Closure $inputHandler */
        $inputHandler = null;
        $this->inputObserver->shouldReceive('addHandler')->with(m::type(\Closure::class))
            ->once()->andReturnUsing(function ($h) use (&$inputHandler) {
                $inputHandler = $h;
            });

        $this->inputObserver->shouldReceive('start')->with()
            ->once()->andReturnUsing(function () use (&$handler, &$inputHandler) {
                // send 1 character
                $inputHandler((object)['char' => 'a']);

                // delete 1 character
                $event = (object)[
                    'type' => 'keyUp',
                    'char' => "\x7f",
                    'stopPropagation' => false,
                ];
                $handler($event);
                self::assertTrue($event->stopPropagation);

                // send 3 other characters
                $inputHandler((object)['char' => 'x']);
                $inputHandler((object)['char' => 'y']);
                $inputHandler((object)['char' => 'z']);
            });

        $result = $this->editline->read(3);

        self::assertSame('xyz', $result);
    }

    /** @test */
    public function writesTheDefaultPrompt()
    {
        $this->console->shouldReceive('write')->with(' ')
            ->once();

        $this->emulateInput('y');
        $this->editline->read(1);
    }

    protected function emulateInput($input)
    {
        /** @var \Closure $handler */
        $handler = null;
        $this->inputObserver->shouldReceive('addHandler')->with(m::type(\Closure::class))
            ->once()->andReturnUsing(function ($h) use (&$handler) {
                $handler = $h;
            });
        $this->inputObserver->shouldReceive('start')->with()
            ->once()->andReturnUsing(function () use (&$handler, $input) {
                while (strlen($input)) {
                    $char = mb_substr($input, 0, 1);
                    $input = mb_substr($input, 1);
                    $this->console->shouldReceive('write')->with($char)->once();
                    $handler((object)['char' => $char]); // magically with 7 characters
                }
            });
    }
}
