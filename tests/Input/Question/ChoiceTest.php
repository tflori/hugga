<?php

namespace Hugga\Test\Input\Question;

use Mockery as m;
use Hugga\Console;
use Hugga\Input\ObserverFaker;
use Hugga\Input\Question\Choice;
use Hugga\Test\TestCase;

class ChoiceTest extends TestCase
{
    public function provideSelectingKeys()
    {
        return [
            ['a', [], 'a'], // no movement
            ['a', ["\e[B"], 'b'], // one cursor down
            ['a', ["\e[F"], 'f'], // end key
            ['b', ["\e[6~"], 'd'], // page down: limit/2 = 2
            ['b', ["\e[A"], 'a'], // one cursor up
            ['a', ["\e[A"], 'f'], // cycling to bottom
            ['a', ["\e[F", "\e[B"], 'a'], // cycling to top
            ['f', ["\e[H"], 'a'], // home key
            ['f', ["\e[5~"], 'd'], // page up
        ];
    }

    public function provideDefaultValues()
    {
        return [
            [true, null],
            [true, 'b'],
            [false, 'b'],
        ];
    }

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

    /** @dataProvider provideDefaultValues
     * @param bool $indexed
     * @param $default
     * @test */
    public function returnsDefaultValue($indexed, $default)
    {
        $choice = new Choice(
            $indexed ? ['a', 'b'] : ['a' => 'Gate A', 'b' => 'Gate B'],
            'Which Gate?',
            $default
        );
        $choice->nonInteractive();

        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn(PHP_EOL);

        $answer = $choice->ask($this->console);

        self::assertSame($default, $answer);
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
    public function returnsTheValue()
    {
        $choice = new Choice(['a' => 'Gate A', 'b' => 'Gate B'], 'Which gate you need?');
        $choice->nonInteractive()->returnValue();

        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn('b' . PHP_EOL);

        $answer = $choice->ask($this->console);

        self::assertSame('Gate B', $answer);
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
        // prepare 28 choices
        $choices = [];
        while (count($choices) < 28) {
            $choices[] = mt_rand(1000, 2000);
            $choices = array_unique($choices);
        }

        $choice = new Choice($choices);
        $choice->nonInteractive()->returnKey();

        $lines = [];
        // a-z for the first 26 choices
        array_push($lines, ...array_map(function ($key, $value) {
            return '   [' . $key . '] ' . $value;
        }, range('a', 'z'), array_slice($choices, 0, 26)));
        // aa for the 27th choice (index 26)
        $lines[] = '  [aa] ' . $choices[26];
        // aa for the 28th choice (index 27)
        $lines[] = '  [ab] ' . $choices[27];

        $this->console->shouldReceive('line')->with(implode(PHP_EOL, $lines), Console::WEIGHT_HIGH)
            ->once();
        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn('ab');

        $answer = $choice->ask($this->console);

        // because of `returnKey()` we should get the original index of the answer 'ab'
        self::assertSame(27, $answer);
    }

    /** @testWith [8, 7]
     *            [12, 5]
     * @param int $amount
     * @param int $default
     * @test */
    public function acceptsKeysAsDefaultValue(int $amount, int $default)
    {
        // prepare $amount choices
        $choices = [];
        while (count($choices) < $amount) {
            $choices[] = mt_rand(1000, 2000);
            $choices = array_unique($choices);
        }

        $choice = new Choice($choices, '', $default);
        $choice->nonInteractive()->returnKey();

        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn(PHP_EOL);

        $answer = $choice->ask($this->console);

        self::assertSame($default, $answer);
    }

    /** @test */
    public function highlightsDefaultSelection()
    {
        $choice = new Choice(['a', 'b'], 'Which gate you need?', 'a');
        $choice->nonInteractive();

        $this->console->shouldReceive('line')->with(
            '  ${invert}[1] a${r}' . PHP_EOL .
            '  [2] b',
            Console::WEIGHT_HIGH
        )->once();
        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn(PHP_EOL);

        $answer = $choice->ask($this->console);

        self::assertSame('a', $answer);
    }

    /***************
     * Interactive *
     ***************/

    /** @var ObserverFaker|m\mock */
    protected $observer;

    protected function setUp()
    {
        $this->console = $this->createConsoleMock(true);
        $this->formatter = $this->consoleMocks['formatter'];
        $this->output = $this->consoleMocks['output'];
        $this->input = $this->consoleMocks['input'];
        $this->error = $this->consoleMocks['error'];
        $this->observer = $this->consoleMocks['observer'];

        $this->console->shouldReceive('readLine')
            ->andReturn(PHP_EOL)->byDefault();
    }

    /** @test */
    public function usesInteractiveMode()
    {
        $this->console->shouldReceive('isInteractive')->with()
            ->once()->andReturn(true);
        $this->console->shouldReceive('getInputObserver')->with()
            ->once()->andReturn($this->observer);

        (new Choice(['a', 'b']))->ask($this->console);
    }

    /** @dataProvider provideDefaultValues
     * @param bool $indexed
     * @param $default
     * @test */
    public function returnsDefaultValueOnEscape($indexed, $default)
    {
        $this->observer->sendKeys("\e");

        $answer = (new Choice(
            $indexed ? ['a', 'b'] : ['a' => 'Gate A', 'b' => 'Gate B'],
            'Which Gate?',
            $default
        ))->ask($this->console);

        self::assertSame($default, $answer);
    }

    /** @test */
    public function returnsDefaultSelected()
    {
        $this->observer->sendKeys("\n");

        $answer = (new Choice(['a', 'b'], '', 'b'))->ask($this->console);

        // default is selected from beginning
        self::assertSame('b', $answer);
    }

    /** @dataProvider provideSelectingKeys
     * @param string $default
     * @param array $keys
     * @param string $expected
     * @test */
    public function returnsSelected(string $default, array $keys, string $expected)
    {
        $this->observer->sendKeys(...$keys, ...["\n"]); // at the end we send always enter

        $answer = (new Choice(['a', 'b', 'c', 'd', 'e', 'f'], '', $default))->limit(5)->ask($this->console);

        self::assertSame($expected, $answer);
    }

    /** @test */
    public function returnsKeysForAssocArrays()
    {
        $this->observer->sendKeys("\n");

        $answer = (new Choice(['a' => 'Gate A', 'b' => 'Gate B'], 'Which gate?', 'b'))->ask($this->console);

        self::assertSame('b', $answer);
    }
}
