<?php

namespace Hugga\Test;

use Hugga\Console;
use Hugga\Input\File as InputHandler;
use Hugga\Input\Question\Simple;
use Hugga\InteractiveInputInterface;
use Hugga\InteractiveOutputInterface;
use Hugga\Output\File as OutputHandler;
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
    public function errorsCanBeHiddenByLowVerbosity()
    {
        $this->console->setVerbosity(Console::WEIGHT_NORMAL);
        $this->console->error('The user made a mistake', Console::WEIGHT_LOWER);

        rewind($this->stderr);
        self::assertSame("", fread($this->stderr, 4096));
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
        $this->console->shouldReceive('isInteractive')->andReturn(true);
        $line = 'John Doe' . PHP_EOL;
        fwrite($this->stdin, $line . 'Jane Doe' . PHP_EOL);
        rewind($this->stdin);

        $answer = $this->console->readLine();

        self::assertSame(rtrim($line), $answer);
    }

    /** @test */
    public function readsACharacter()
    {
        $this->console->shouldReceive('isInteractive')->andReturn(true);
        fwrite($this->stdin, 'foo bar');
        rewind($this->stdin);

        $answer = $this->console->read(3);

        self::assertSame('foo', $answer);
    }

    /** @test */
    public function readsUntilSequence()
    {
        $this->console->shouldReceive('isInteractive')->andReturn(true);
        fwrite($this->stdin, 'lorem ipsum dolor sit amet' . PHP_EOL . PHP_EOL);
        rewind($this->stdin);

        $answer = $this->console->readUntil(PHP_EOL . PHP_EOL);

        self::assertSame('lorem ipsum dolor sit amet', $answer);
    }

    /** @test */
    public function acceptsOutputInterfaceForStdout()
    {
        $outputHandler = new OutputHandler($this->console, $this->stdout);

        $this->console->setStdout($outputHandler);

        self::assertSame($outputHandler, $this->console->getOutput());
    }

    /** @test */
    public function acceptsInputInterfaceForStdin()
    {
        $inputHandler = new InputHandler($this->console, $this->stdin);

        $this->console->setStdin($inputHandler);

        self::assertSame($inputHandler, $this->console->getInput());
    }

    /** @test */
    public function fallsBackToFileHandler()
    {
        $this->console->setStdin(fopen('php://memory', 'w+'));
        $this->console->setStdout(fopen('php://memory', 'w+'));

        self::assertInstanceOf(InputHandler::class, $this->console->getInput());
        self::assertInstanceOf(OutputHandler::class, $this->console->getOutput());
    }

    /** @test */
    public function acceptsOutputInterfaceForStderr()
    {
        $outputHandler = new OutputHandler($this->console, $this->stderr);

        $this->console->setStderr($outputHandler);

        self::assertSame($outputHandler, $this->console->getStderr());
    }

    /** @test */
    public function returnsNullWhenInputIsNotCompatible()
    {
        $result = $this->console->getInputObserver();

        self::assertNull($result);
    }

    /** @test */
    public function asksTheQuestion()
    {
        $question = m::mock(Simple::class);
        $question->shouldReceive('ask')->with($this->console)
            ->once()->andReturn('Answer');
        $this->console->shouldReceive('isInteractive')->with()
            ->once()->andReturn(true);

        $result = $this->console->ask($question);

        self::assertSame('Answer', $result);
    }

    /** @test */
    public function createsASimpleQuestionAndAsks()
    {
        $this->console->shouldReceive('isInteractive')->andReturn(true);
        fwrite($this->stdin, 'Answer' . PHP_EOL);
        rewind($this->stdin);

        $result = $this->console->ask('Whats up bro?');

        self::assertSame('Answer', $result);
    }

    /** @test */
    public function deletesStrlenCharacters()
    {
        $this->console->disableAnsi();
        $this->console->write('foo bär');

        $this->console->delete('bar');

        rewind($this->stdout);
        self::assertSame('foo ', fread($this->stdout, 4096));
    }

    /** @test */
    public function isInteractiveWhenStdinAndStdoutAre()
    {
        self::assertFalse($this->console->isInteractive());
        $this->console->setStdin(m::mock(InteractiveInputInterface::class));
        self::assertFalse($this->console->isInteractive());
        $this->console->setStdout(m::mock(InteractiveOutputInterface::class));
        self::assertTrue($this->console->isInteractive());
    }

    /** @test */
    public function isNeverInteractive()
    {
        $this->console->setStdin(m::mock(InteractiveInputInterface::class));
        $this->console->setStdout(m::mock(InteractiveOutputInterface::class));

        $this->console->nonInteractive();

        self::assertFalse($this->console->isInteractive());
    }
}
