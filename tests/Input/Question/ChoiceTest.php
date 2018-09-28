<?php

namespace Hugga\Test\Input\Question;

use Hugga\Console;
use Hugga\Input\Question\Choice;
use Hugga\Test\TestCase;

class ChoiceTest extends TestCase
{
    /*******************
     * Non-Interactive *
     *******************/

    /** @test */
    public function usesNonInteractiveMode()
    {
        $this->console->shouldReceive('isInteractive')->with()
            ->once()->andReturn(false);
        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn(PHP_EOL);

        (new Choice(['a', 'b']))->ask($this->console);
    }

    /** @test */
    public function writesOutQuestionAndChoices()
    {
        $choice = new Choice(['a', 'b'], 'Which gate you need?');
        $choice->nonInteractive();

        $this->console->shouldReceive('line')->with('Which gate you need?', Console::WEIGHT_HIGH)
            ->once()->ordered();
        $this->console->shouldReceive('line')->with('  [1] a' . PHP_EOL . '  [2] b', Console::WEIGHT_HIGH)
            ->once()->ordered();

        $choice->ask($this->console);
    }

    /** @test */
    public function returnsTheDefault()
    {
        $choice = new Choice(['a', 'b'], 'Which gate you need?', 'a');
        $choice->nonInteractive();

        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn(PHP_EOL);

        $answer = $choice->ask($this->console);

        self::assertSame('a', $answer);
    }

    /** @test */
    public function returnsNullWithoutDefault()
    {
        $choice = new Choice(['a', 'b'], 'Which gate you need?');
        $choice->nonInteractive();

        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn(PHP_EOL);

        $answer = $choice->ask($this->console);

        self::assertNull($answer);
    }

    /** @test */
    public function acceptsKeyAsAnswer()
    {
        $choice = new Choice(['a', 'b'], 'Which gate you need?');
        $choice->nonInteractive();

        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn('1' . PHP_EOL);

        $answer = $choice->ask($this->console);

        self::assertSame('a', $answer);
    }

    /** @test */
    public function acceptsValueAsAnswer()
    {
        $choice = new Choice(['a', 'b'], 'Which gate you need?');
        $choice->nonInteractive();

        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn('b' . PHP_EOL);

        $answer = $choice->ask($this->console);

        self::assertSame('b', $answer);
    }

    /** @test */
    public function returnsTheKey()
    {
        $choice = new Choice(['a', 'b'], 'Which gate you need?');
        $choice->nonInteractive()->returnKey();

        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn('b' . PHP_EOL);

        $answer = $choice->ask($this->console);

        self::assertSame(1, $answer);
    }

    /** @test */
    public function repeatsTheQuestionForInvalidAnswers()
    {
        $choice = new Choice(['a', 'b'], 'Which gate you need?');
        $choice->nonInteractive();

        $this->console->shouldReceive('readLine')->with('> ')
            ->twice()->andReturn('c' . PHP_EOL, 'a' . PHP_EOL);
        $this->console->shouldReceive('line')->with('${red}Unknown choice c', Console::WEIGHT_HIGH)
            ->once();

        $answer = $choice->ask($this->console);

        self::assertSame('a', $answer);
    }

    /** @test */
    public function usesCharsForGreaterAmountOfChoices()
    {
        // prepare 12 choices
        $choices = [];
        while (count($choices) < 12) {
            $choices[] = mt_rand(1000, 2000);
            $choices = array_unique($choices);
        }

        $choice = new Choice($choices);
        $choice->nonInteractive()->returnKey();

        $this->console->shouldReceive('line')->with(
            implode(PHP_EOL, array_map(function ($key, $value) {
                return '  [' . $key . '] ' . $value;
            }, range('a', 'l'), $choices)),
            Console::WEIGHT_HIGH
        )->once();
        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn('h');

        $answer = $choice->ask($this->console);

        self::assertSame(7, $answer);
    }
}
