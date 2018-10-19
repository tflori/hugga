<?php

namespace Hugga;

use Hugga\Input\Editline;
use Hugga\Input\File as InputHandler;
use Hugga\Input\Observer;
use Hugga\Input\Question\Simple;
use Hugga\Input\Readline;
use Hugga\Output\File as OutputHandler;
use Hugga\Output\Tty;
use Psr\Log\LoggerInterface;

class Console
{
    const WEIGHT_HIGH = 300;
    const WEIGHT_HIGHER = 250;
    const WEIGHT_NORMAL = 200;
    const WEIGHT_LOWER = 150;
    const WEIGHT_LOW = 125;
    const WEIGHT_DEBUG = 100;

    const VERBOSITY_ORDER = [
        self::WEIGHT_HIGH,
        self::WEIGHT_HIGHER,
        self::WEIGHT_NORMAL,
        self::WEIGHT_LOWER,
        self::WEIGHT_LOW,
        self::WEIGHT_DEBUG,
    ];

    /** @var LoggerInterface */
    protected $logger;

    /** @var Formatter */
    protected $formatter;

    /** @var int */
    protected $verbosity = self::WEIGHT_NORMAL;

    /** @var bool */
    protected $logMessages = false;

    /** @var OutputInterface */
    protected $stdout;

    /** @var InputInterface */
    protected $stdin;

    /** @var OutputInterface */
    protected $stderr;

    /** @var bool  */
    protected $ansiEnabled = true;

    /** @var DrawingInterface[] */
    protected $drawings = [];

    /** @var bool */
    protected $interactive = true;

    /**
     * Console constructor.
     *
     * @param LoggerInterface $logger
     * @param Formatter       $formatter
     */
    public function __construct(LoggerInterface $logger = null, Formatter $formatter = null)
    {
        $this->logger = $logger;
        $this->formatter = $formatter ?? new Formatter();
        $this->setStdout(STDOUT);
        $this->setStdin(STDIN);
        $this->setStderr(STDERR);
    }

    /**
     * @param $resource
     * @return bool
     * @codeCoverageIgnore polyfill from https://github.com/symfony/polyfill/blob/master/src/Php72/Php72.php
     */
    public static function isTty($resource)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException(sprintf(
                'Argument 1 passed to %s has to be of the type resource, %s given',
                __METHOD__,
                gettype($resource)
            ));
        }

        if (function_exists('stream_isatty')) {
            return stream_isatty($resource);
        }

        if ('\\' === DIRECTORY_SEPARATOR) {
            $stat = @fstat($resource);
            // Check if formatted mode is S_IFCHR
            return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
        }
        return function_exists('posix_isatty') && @posix_isatty($resource);
    }

    /**
     * Write $message to stdout
     *
     * @param string $message
     * @param int    $weight
     */
    public function write(string $message, int $weight = self::WEIGHT_NORMAL): void
    {
        $this->log($weight, $message);

        if ($this->verbosity > $weight) {
            return;
        }

        $this->cleanDrawings();
        $this->stdout->write($this->format($message));
        $this->drawDrawings();
    }

    /**
     * Redraw all registered drawings
     *
     * Call this method if your drawing got updated.
     */
    public function redraw()
    {
        $this->refreshDrawings();
    }

    /**
     * Register $drawing
     *
     * Returns false when drawing was already added.
     *
     * @param DrawingInterface $drawing
     * @return bool
     */
    public function addDrawing(DrawingInterface $drawing): bool
    {
        $hash = spl_object_hash($drawing);
        if (isset($this->drawings[$hash])) {
            return false;
        }

        $this->drawings[$hash] = [
            'drawing' => $drawing,
            'lines' => 0,
        ];
        $this->refreshDrawings();
        return true;
    }

    /**
     * Remove $drawing
     *
     * When the output is not interactive the drawing will now be added to the output.
     *
     * Returns false when the drawing was not registered.
     *
     * @param DrawingInterface $drawing
     * @return bool
     */
    public function removeDrawing(DrawingInterface $drawing): bool
    {
        $hash = spl_object_hash($drawing);
        if (!isset($this->drawings[$hash])) {
            return false;
        }

        $this->cleanDrawings();
        $this->stdout->write($this->format($drawing->getText()) . PHP_EOL);
        unset($this->drawings[$hash]);
        $this->drawDrawings();
        return true;
    }

    /**
     * @param int|string $count
     */
    public function delete($count)
    {
        if (is_string($count)) {
            $count = mb_strlen($count);
        }

        $this->stdout->delete($count);
    }

    /**
     * @codeCoverageIgnore alias to ->getOutput()->deleteLine()
     */
    public function deleteLine()
    {
        $this->stdout->deleteLine();
    }

    /**
     * Read a line from InputHandler
     *
     * @param string|null $prompt
     * @return string
     */
    public function readLine(string $prompt = null): string
    {
        return $this->stdin->readLine($prompt);
    }

    /**
     * Read one or more characters from InputHandler
     *
     * @param int $count
     * @param string|null $prompt
     * @return string
     */
    public function read(int $count = 1, string $prompt = null): string
    {
        return $this->stdin->read($count, $prompt);
    }

    /**
     * Read until $sequence from InputHandler
     *
     * @param string $sequence
     * @param string|null $prompt
     * @return string
     */
    public function readUntil(string $sequence, string $prompt = null): string
    {
        return $this->stdin->readUntil($sequence, $prompt);
    }

    /**
     * Write $message to stderr
     *
     * @param string $message
     * @param int    $weight
     */
    public function writeError(string $message, int $weight = self::WEIGHT_HIGH): void
    {
        $this->log($weight, $message);
        $this->cleanDrawings();
        $this->stderr->write($this->format($message));
        $this->drawDrawings();
    }

    public function error(string $message, int $weight = self::WEIGHT_HIGH): void
    {
        $lines = array_map('rtrim', explode(PHP_EOL, $message));
        $maxLength = max(array_map('strlen', $lines));
        $message = '${bg:red}' . str_repeat(' ', $maxLength + 4) . '${r}' . PHP_EOL;
        foreach ($lines as $line) {
            $message .= '${fg:white;bg:red;bold}  ' .
                        sprintf('%-' . $maxLength . 's', $line) .
                        '  ${r}' . PHP_EOL;
        }
        $message .= '${bg:red}' . str_repeat(' ', $maxLength + 4) . PHP_EOL;

        $this->writeError($message, $weight);
    }

    /**
     * Get the string length of $message without formatting
     *
     * @param string $message
     * @return int
     */
    public function strLen(string $message)
    {
        return mb_strlen($this->formatter->stripFormatting($message));
    }

    /**
     * Shortcut to ->write('${green;bold}Your message' . PHP_EOL)
     *
     * @param string $message
     * @codeCoverageIgnore trivial
     */
    public function info(string $message)
    {
        $this->line('${green;bold}' . $message, self::WEIGHT_NORMAL);
    }

    /**
     * Shortcut to ->write('${red;bold}Your message' . PHP_EOL, WEIGHT_HIGHER);
     *
     * @param string $message
     * @codeCoverageIgnore trivial
     */
    public function warn(string $message)
    {
        $this->line('${yellow;bold}' . $message, self::WEIGHT_HIGHER);
    }

    /**
     * Shortcut to ->write('Your message' . PHP_EOL);
     *
     * @param string $message
     * @codeCoverageIgnore trivial
     */
    public function line(string $message, int $weight = self::WEIGHT_NORMAL): void
    {
        $this->write($message . PHP_EOL, $weight);
    }

    /**
     * Ask a simple question or the given question.
     *
     * @param QuestionInterface|string $question
     * @param mixed $default
     * @return mixed
     */
    public function ask($question, $default = null)
    {
        $interactive = $this->isInteractive();
        if ($question instanceof QuestionInterface) {
            return $interactive ? $question->ask($this) : $question->getDefault();
        }

        return $interactive ? (new Simple($question, $default))->ask($this) : $default;
    }

    /**
     * Set the verbosity
     *
     * @param $weight
     * @return $this
     */
    public function setVerbosity(int $weight)
    {
        $this->verbosity = $weight;
        return $this;
    }

    /**
     * Disable interactive mode
     *
     * Questions will return default or null.
     *
     * @return $this
     */
    public function nonInteractive()
    {
        $this->interactive = false;
        return $this;
    }

    /**
     * Increase the verbosity according to VERBOSITY_ORDER
     *
     * @return $this
     */
    public function increaseVerbosity()
    {
        $p = array_search($this->verbosity, self::VERBOSITY_ORDER);
        $this->verbosity = self::VERBOSITY_ORDER[$p + 1] ?? $this->verbosity;
        return $this;
    }

    /**
     * @return int
     * @codeCoverageIgnore  trivial
     */
    public function getVerbosity(): int
    {
        return $this->verbosity;
    }

    public function disableAnsi(bool $disabled = true)
    {
        $this->ansiEnabled = !$disabled;
        return $this;
    }

    /**
     * Set the resource for stdout
     *
     * @param resource|OutputInterface $stdout
     * @return $this
     */
    public function setStdout($stdout)
    {
        if ($stdout instanceof OutputInterface) {
            $this->stdout = $stdout;
            return $this;
        }

        self::assertResource($stdout, __METHOD__);
        $this->stdout = Tty::isCompatible($stdout)
            ? new Tty($this, $stdout) : new OutputHandler($this, $stdout);
        return $this;
    }

    public function getOutput(): OutputInterface
    {
        return $this->stdout;
    }

    /**
     * Set the resource for stdin
     *
     * @param resource|InputInterface $stdin
     * @return $this
     */
    public function setStdin($stdin)
    {
        if ($stdin instanceof InputInterface) {
            $this->stdin = $stdin;
            return $this;
        }

        self::assertResource($stdin, __METHOD__);
        foreach ([Readline::class, Editline::class] as $handler) {
            if ($handler::isCompatible($stdin)) {
                $this->stdin = new $handler($this, $stdin);
                return $this;
            }
        }
        $this->stdin = new InputHandler($this, $stdin);
        return $this;
    }

    public function getInput(): InputInterface
    {
        return $this->stdin;
    }

    /**
     * Creates an input observer if compatible
     *
     * Returns null if input is not compatible.
     *
     * @return ?Observer
     */
    public function getInputObserver(): ?Observer
    {
        if (!Observer::isCompatible($this->stdin)) {
            return null;
        }
        // @codeCoverageIgnoreStart
        return new Observer($this->stdin);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Set the resource for stderr
     *
     * @param resource|OutputInterface $stderr
     * @return $this
     */
    public function setStderr($stderr)
    {
        if ($stderr instanceof OutputInterface) {
            $this->stderr = $stderr;
            return $this;
        }

        self::assertResource($stderr, __METHOD__);
        $this->stderr = Tty::isCompatible($stderr)
            ? new Tty($this, $stderr) : new OutputHandler($this, $stderr);
        return $this;
    }

    public function getStderr(): OutputInterface
    {
        return $this->stderr;
    }

    /**
     * Format a message if ansi is enabled
     *
     * @param $message
     * @return string
     */
    public function format($message)
    {
        return $this->ansiEnabled ? $this->formatter->format($message) : $this->formatter->stripFormatting($message);
    }

    public function isInteractive()
    {
        return $this->interactive &&
               $this->stdout instanceof InteractiveOutputInterface &&
               $this->stdin instanceof InteractiveInputInterface;
    }

    /**
     * @param resource $resource
     * @param string $method
     * @param int $argn
     * @codeCoverageIgnore trivial code for missing resource type hint
     */
    protected static function assertResource($resource, $method, $argn = 1)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException(sprintf(
                'Argument %d passed to %s has to be of the type resource, %s given',
                $argn,
                $method,
                gettype($resource)
            ));
        }
    }

    /**
     * Clean registered drawings from current output
     */
    protected function cleanDrawings()
    {
        if (!$this->stdout instanceof InteractiveOutputInterface || empty($this->drawings)) {
            return;
        }

        $lines = array_sum(array_map(function ($d) {
            return $d['lines'];
        }, $this->drawings));

        $this->stdout->deleteLines($lines);
    }

    /**
     * Draw registered drawings to output
     */
    protected function drawDrawings()
    {
        if (!$this->stdout instanceof InteractiveOutputInterface || empty($this->drawings)) {
            return;
        }

        $output = implode(PHP_EOL, array_map(function ($d) {
            return $this->format($d['drawing']->getText());
        }, $this->drawings));

        $this->stdout->write($output);
    }

    protected function refreshDrawings()
    {
        if (!$this->stdout instanceof InteractiveOutputInterface || empty($this->drawings)) {
            return;
        }

        $lines = 0;
        $texts = [];
        foreach ($this->drawings as $hash => $d) {
            $lines += $d['lines'];
            $text = $this->format($d['drawing']->getText());
            $this->drawings[$hash]['lines'] = substr_count($text, PHP_EOL) + 1;
            $texts[] = $text;
        }

        $this->stdout->deleteLines($lines, implode(PHP_EOL, $texts));
    }

    /**
     * Log $message to logger if enabled
     *
     * @param int    $weight
     * @param string $message
     */
    protected function log(int $weight, string $message)
    {
        if (!$this->logMessages || !$this->logger) {
            return;
        }

        $this->logger->log($weight, trim($this->formatter->stripFormatting($message)));
    }

    /**
     * Set the logger and enable logging
     *
     * @param LoggerInterface $logger
     * @param bool            $logMessages
     * @return $this
     */
    public function setLogger(LoggerInterface $logger, bool $logMessages = true)
    {
        $this->logger = $logger;
        $this->logMessages = $logMessages;
        return $this;
    }

    /**
     * Enable or disable logging
     *
     * @param bool $enabled
     * @codeCoverageIgnore trivial
     * @return $this
     */
    public function logMessages(bool $enabled)
    {
        $this->logMessages = $enabled;
        return $this;
    }
}
