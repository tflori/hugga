<?php

namespace Hugga\Test\Input;

use Hugga\Console;
use Hugga\Input\Question\Simple;
use Hugga\Test\TestCase;

class QuestionTest extends TestCase
{
    /** @test */
    public function trimsTheAnswer()
    {
        $this->console->shouldReceive('readLine')->with()
            ->once()->andReturn('John Doe');

        $answer = (new \Hugga\Input\Question\Simple())->ask($this->console);

        self::assertSame('John Doe', $answer);
    }

    /** @test */
    public function outputsTheQuestion()
    {
        $this->console->shouldReceive('write')->with('What is your name?', Console::WEIGHT_HIGH)
            ->once()->andReturnSelf();
        $this->console->shouldReceive('readLine')->with()
            ->once()->andReturn('John Doe')->ordered();

        (new Simple('What is your name?'))->ask($this->console);
    }

    /** @test */
    public function returnsDefault()
    {
        $this->console->shouldReceive('readLine')->with()
            ->once()->andReturn('')->ordered();

        $answer = (new \Hugga\Input\Question\Simple('What is your name?', 'John Doe'))->ask($this->console);

        self::assertSame('John Doe', $answer);
    }

    /** @test */
    public function outputsTheDefault()
    {
        $this->console->shouldReceive('write')->with('What is your name? [John Doe]', Console::WEIGHT_HIGH)
            ->once()->andReturnSelf();
        $this->console->shouldReceive('readLine')->with()
            ->once()->andReturn('')->ordered();

        (new Simple('What is your name?', 'John Doe'))->ask($this->console);
    }
}
