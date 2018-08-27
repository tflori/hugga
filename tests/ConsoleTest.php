<?php

namespace Hugga\Test;

use Hugga\Console;
use Hugga\Input\FileHandler as InputHandler;
use Hugga\Input\Question\Simple;
use Hugga\Output\FileHandler as OutputHandler;
use Mockery as m;
use Psr\Log\LoggerInterface;

class ConsoleTest extends TestCase
{
    /** @test */
    public function writesToStdout()
    {
        $message = 'foo bar';

        $this->console->write($message);

        rewind($this->stdout);
        self::assertSame($message, fread($this->stdout, strlen($message)));
    }

    /** @test */
    public function writesFormattedMessage()
    {
        $message = '${red}foo bar';
        $result = "\e[31mfoo bar\e[0m";
        $this->formatter->shouldReceive('format')->with($message)
            ->once()->andReturn($result);

        $this->console->write($message);

        rewind($this->stdout);
        self::assertSame($result, fread($this->stdout, 4096));
    }

    /** @test */
    public function logsToLogger()
    {
        $message = 'foo bar';
        /** @var m\mock|LoggerInterface $logger */
        $logger = m::mock(LoggerInterface::class);
        $logger->shouldReceive('log')->with(Console::WEIGHT_NORMAL, $message)->once();

        $this->console->setLogger($logger);
        $this->console->write($message);
    }

    /** @test */
    public function stripsFormattingForLogger()
    {
        $message = '${red}foo bar';
        $result = "foo bar";
        $this->formatter->shouldReceive('stripFormatting')->with($message)
            ->once()->andReturn($result);
        /** @var m\mock|LoggerInterface $logger */
        $logger = m::mock(LoggerInterface::class);
        $logger->shouldReceive('log')->with(Console::WEIGHT_NORMAL, $result)->once();

        $this->console->setLogger($logger);
        $this->console->write($message);
    }

    /** @test */
    public function doesNotOutputWhenVerbosityIsToHigh()
    {
        $message = 'foo bar';

        $this->console->setVerbosity(Console::WEIGHT_NORMAL);
        $this->console->write($message, Console::WEIGHT_LOW);

        rewind($this->stdout);
        self::assertEmpty(fread($this->stdout, strlen($message)));
    }

    /** @test */
    public function writesToStderr()
    {
        $message = 'foo bar';

        $this->console->writeError($message);

        rewind($this->stderr);
        self::assertSame($message, fread($this->stderr, strlen($message)));
    }

    /** @test */
    public function writesFormattedError()
    {
        $message = '${red}foo bar';
        $result = "\e[31mfoo bar\e[0m";
        $this->formatter->shouldReceive('format')->with($message)
            ->once()->andReturn($result);

        $this->console->writeError($message);

        rewind($this->stderr);
        self::assertSame($result, fread($this->stderr, 4096));
    }

    /** @test */
    public function logsErrorToLogger()
    {
        $message = 'foo bar';
        /** @var m\mock|LoggerInterface $logger */
        $logger = m::mock(LoggerInterface::class);
        $logger->shouldReceive('log')->with(Console::WEIGHT_HIGH, $message)->once();

        $this->console->setLogger($logger);
        $this->console->writeError($message);
    }

    /** @test */
    public function ignoresVerbosity()
    {
        $message = 'foo bar';

        $this->console->setVerbosity(Console::WEIGHT_NORMAL);
        $this->console->writeError($message, Console::WEIGHT_LOW);

        rewind($this->stderr);
        self::assertSame($message, fread($this->stderr, strlen($message)));
    }

    /** @test */
    public function addsErrorFormatting()
    {
        $message = 'foo bar';

        $this->console->error($message);

        rewind($this->stderr);
        self::assertSame(
            "\e[41m" . str_repeat(' ', strlen($message) + 4) . "\e[0m\n" .
            "\e[97m\e[41m\e[1m  " . $message . "  \e[0m\n" .
            "\e[41m" . str_repeat(' ', strlen($message) + 4) . "\e[0m\n",
            fread($this->stderr, 4096)
        );
    }

    /** @test */
    public function debugIsShownAtThirdLevel()
    {
        $message = 'foo bar';

        $this->console->write($message, Console::WEIGHT_DEBUG);

        rewind($this->stdout);
        self::assertEmpty(fread($this->stdout, 4096));

        $this->console->increaseVerbosity()
            ->increaseVerbosity()
            ->increaseVerbosity()
            ->write($message, Console::WEIGHT_DEBUG);

        rewind($this->stdout);
        self::assertSame($message . "\e[0m", fread($this->stdout, 4096));
    }

    /** @test */
    public function stripsFormattingWhenAnsiIsDisabled()
    {
        $message = 'foo bar';

        $this->console->disableAnsi();
        $this->console->write('${bold}' . $message);

        rewind($this->stdout);
        self::assertSame($message, fread($this->stdout, 4096));
    }

    /** @test */
    public function readsALine()
    {
        $line = 'John Doe' . PHP_EOL;
        fwrite($this->stdin, $line . 'Jane Doe' . PHP_EOL);
        rewind($this->stdin);

        $answer = $this->console->readLine();

        self::assertSame(rtrim($line), $answer);
    }

    /** @test */
    public function readsACharacter()
    {
        fwrite($this->stdin, 'foo bar');
        rewind($this->stdin);

        $answer = $this->console->read(3);

        self::assertSame('foo', $answer);
    }

    /** @test */
    public function readsUntilSequence()
    {
        fwrite($this->stdin, 'lorem ipsum dolor sit amet' . PHP_EOL . PHP_EOL);
        rewind($this->stdin);

        $answer = $this->console->readUntil(PHP_EOL . PHP_EOL);

        self::assertSame('lorem ipsum dolor sit amet', $answer);
    }

    /** @test */
    public function acceptsOutputInterfaceForStdout()
    {
        $outputHandler = new OutputHandler($this->stdout);

        $this->console->setStdout($outputHandler);

        self::assertSame($outputHandler, $this->console->getStdout());
    }

    /** @test */
    public function acceptsInputInterfaceForStdin()
    {
        $inputHandler = new InputHandler($this->stdin);

        $this->console->setStdin($inputHandler);

        self::assertSame($inputHandler, $this->console->getStdin());
    }

    /** @test */
    public function acceptsOutputInterfaceForStderr()
    {
        $outputHandler = new OutputHandler($this->stderr);

        $this->console->setStderr($outputHandler);

        self::assertSame($outputHandler, $this->console->getStderr());
    }

    /** @test */
    public function throwsWhenStdIsNotCompatible()
    {
        self::expectException(\LogicException::class);

        $this->console->getInputObserver();
    }

    /** @test */
    public function asksTheQuestion()
    {
        $question = m::mock(Simple::class);
        $question->shouldReceive('ask')->with($this->console)
            ->once()->andReturn('Answer');

        $result = $this->console->ask($question);

        self::assertSame('Answer', $result);
    }

    /** @test */
    public function createsASimpleQuestionAndAsks()
    {
        fwrite($this->stdin, 'Answer' . PHP_EOL);
        rewind($this->stdin);

        $result = $this->console->ask('Whats up bro?');

        self::assertSame('Answer', $result);
    }
}
