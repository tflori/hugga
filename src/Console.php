<?php

namespace Hugga;

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

    /** @var bool|resource */
    protected $stdout = STDOUT;

    /** @var bool|resource */
    protected $stdin = STDIN;

    /** @var bool|resource */
    protected $stderr = STDERR;

    /** @var bool  */
    protected $ansiEnabled = true;

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

        fwrite($this->stdout, $this->format($message));
    }

    /**
     * @return string
     */
    public function waitLine()
    {
        return (string)fgets($this->stdin);
    }

    public function waitChars($length = 1)
    {
        system("stty -icanon");
        $answer = (string)fread($this->stdin, $length);
        system('stty sane');
        return $answer;
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
        fwrite($this->stderr, $this->format($message));
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
     * @param resource $stdout
     * @return $this
     */
    public function setStdout($stdout)
    {
        self::assertResource($stdout, __METHOD__);
        $this->stdout = $stdout;
        return $this;
    }

    /**
     * Set the resource for stdin
     *
     * @param resource $stdin
     * @return $this
     */
    public function setStdin($stdin)
    {
        self::assertResource($stdin, __METHOD__);
        $this->stdin = $stdin;
        return $this;
    }

    /**
     * Set the resource for stderr
     *
     * @param resource $stderr
     * @return $this
     */
    public function setStderr($stderr)
    {
        self::assertResource($stderr, __METHOD__);
        $this->stderr = $stderr;
        return $this;
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
     * Format a message if ansi is enabled
     *
     * @param $message
     * @return string
     */
    protected function format($message)
    {
        return $this->ansiEnabled ? $this->formatter->format($message) : $this->formatter->stripFormatting($message);
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
