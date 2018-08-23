<?php

namespace Hugga\Test;

use Hugga\Console;
use Hugga\Question;

class QuestionTest extends TestCase
{
    /** @test */
    public function trimsTheAnswer()
    {
        $this->console->shouldReceive('waitLine')->with()
            ->once()->andReturn('John Doe' . PHP_EOL);

        $answer = (new Question())->ask($this->console);

        self::assertSame('John Doe', $answer);
    }

    /** @test */
    public function outputsTheQuestion()
    {
        $this->console->shouldReceive('write')->with('What is your name? ', Console::WEIGHT_HIGH)
            ->once()->andReturnSelf();
        $this->console->shouldReceive('waitLine')->with()
            ->once()->andReturn('John Doe' . PHP_EOL)->ordered();

        (new Question('What is your name?'))->ask($this->console);
    }

    /** @test */
    public function returnsDefault()
    {
        $this->console->shouldReceive('waitLine')->with()
            ->once()->andReturn(PHP_EOL)->ordered();

        $answer = (new Question('What is your name?' , 'John Doe'))->ask($this->console);

        self::assertSame('John Doe', $answer);
    }

    /** @test */
    public function outputsTheDefault()
    {
        $this->console->shouldReceive('write')->with('What is your name? [John Doe] ', Console::WEIGHT_HIGH)
            ->once()->andReturnSelf();
        $this->console->shouldReceive('waitLine')->with()
            ->once()->andReturn(PHP_EOL)->ordered();

        (new Question('What is your name?', 'John Doe'))->ask($this->console);
    }
}