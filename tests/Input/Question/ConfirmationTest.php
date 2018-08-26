<?php

namespace Hugga\Test\Input\Question;

use Hugga\Console;
use Hugga\Input\Question\Confirmation;
use Hugga\Test\TestCase;

class ConfirmationTest extends TestCase
{
    /** @test */
    public function returnsFalseByDefault()
    {
        $this->console->shouldReceive('read')->with()
            ->once()->andReturn(PHP_EOL);

        $answer = (new Confirmation('Confirm?'))->ask($this->console);

        self::assertFalse($answer);
    }

    /** @test */
    public function returnsDefault()
    {
        $this->console->shouldReceive('read')->with()
            ->once()->andReturn('x');

        $answer = (new Confirmation('Confirm?', true))->ask($this->console);

        self::asserttrue($answer);
    }

    /** @test */
    public function returnsTrue()
    {
        $this->console->shouldReceive('read')->with()
            ->once()->andReturn('y');

        $answer = (new \Hugga\Input\Question\Confirmation('Confirm?', false))->ask($this->console);

        self::assertTrue($answer);
    }

    /** @test */
    public function returnsFalse()
    {
        $this->console->shouldReceive('read')->with()
            ->once()->andReturn('n');

        $answer = (new \Hugga\Input\Question\Confirmation('Confirm?', true))->ask($this->console);

        self::assertFalse($answer);
    }

    /** @dataProvider provideCustomCharacters
     * @param $true
     * @param $false
     * @param $default
     * @param $char
     * @param $expected
     * @test */
    public function changeCharacters($true, $false, $default, $char, $expected)
    {
        $this->console->shouldReceive('write')
            ->with(sprintf('Simple text [ %s / %s ] ', $true, $false), Console::WEIGHT_HIGH)
            ->once()->ordered();
        $this->console->shouldReceive('read')->with()
            ->once()->andReturn($char)->ordered();
        $question = new \Hugga\Input\Question\Confirmation('Simple text', $default);

        $question->setCharacters($true, $false);
        $answer = $question->ask($this->console);

        self::assertSame($expected, $answer);
    }

    public function provideCustomCharacters()
    {
        return [
            ['J', 'n', true, 'x', true],
            ['j', 'N', false, 'j', true],
            ['J', 'n', true, 'n', false],
            ['C', 's', true, 'n', true], // continue or stop
            ['C', 's', true, 's', false],
        ];
    }
}
