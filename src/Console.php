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

    protected static $formats = [
        'reset' => '0',
        'r' => '0', // shortcut for reset
        'bold' => '1',
        'b' => '1', // shortcut for bold
        'underline' => '4',
        'u' => '4', // shortcut for underline
        'invert' => '7',
        'hidden' => '8',
    ];

    protected static $fgColors = [
        'default' => '39',
        'black' => '30',
        'red' => '31',
        'green' => '32',
        'yellow' => '33',
        'blue' => '34',
        'magenta' => '35',
        'cyan' => '36',
        'grey' => '37',
        'dark-gray' => '90',
        'light-red' => '91',
        'light-green' => '92',
        'light-yellow' => '93',
        'light-blue' => '94',
        'light-magenta' => '95',
        'light-cyan' => '96',
        'white' => '97',
    ];

    protected static $bgColors = [
        'default' => '49',
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'grey' => '47',
        'dark-gray' => '100',
        'light-red' => '101',
        'light-green' => '102',
        'light-yellow' => '103',
        'light-blue' => '104',
        'light-magenta' => '105',
        'light-cyan' => '106',
        'white' => '107',
    ];

    /** @var int */
    protected $verbosity = self::WEIGHT_NORMAL;

    /** @var bool */
    protected $logMessages = false;

    /** @var LoggerInterface */
    protected $logger;

    /** @var bool|resource */
    protected $stdout = STDOUT;

    /** @var bool|resource */
    protected $stdin = STDIN;

    /** @var bool|resource */
    protected $stderr = STDERR;

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

    public function write(string $message, int $weight = self::WEIGHT_NORMAL): void
    {
//        if ($this->logMessages && $this->logger) {
//            $this->logger->log($weight, rtrim($this->formatMessage($message, true), PHP_EOL));
//        }

//        if ($this->verbosity > $weight) {
//            return;
//        }

        fwrite($this->stdout, $this->formatMessage($message));
    }

//    public function error(string $message, int $weight = self::WEIGHT_HIGH): void
//    {
//        $message = rtrim($message);
//
//        if ($this->logMessages && $this->logger) {
//            $this->logger->log($weight, $this->formatMessage($message, true));
//        }
//
//        // add default formatting if no format is given
//        if (strpos($message, '${') === false) {
//            $lines = array_map('rtrim', explode(PHP_EOL, $message));
//            $maxLength = max(array_map('strlen', $lines));
//            $message = '${bg:red}' . str_repeat(' ', $maxLength + 4) . '${r}' . PHP_EOL;
//            foreach ($lines as $line) {
//                $message .= '${fg:white;bg:red;bold}  ' .
//                            sprintf('%-' . $maxLength . 's', $line) .
//                            '  ${r}' . PHP_EOL;
//            }
//            $message .= '${bg:red}' . str_repeat(' ', $maxLength + 4) . '${r}' . PHP_EOL;
//        }
//
//        fwrite(STDERR, $this->formatMessage($message));
//    }
//
//    public function increaseVerbosity()
//    {
//        $this->verbosity = self::VERBOSITY_ORDER[array_search($this->verbosity, self::VERBOSITY_ORDER) + 1]
//                           ?? $this->verbosity;
//    }
//
//    /**
//     * Set the verbosity
//     *
//     * @param $weight
//     */
//    public function setVerbosity(int $weight)
//    {
//        $this->verbosity = $weight;
//    }
//
    protected function formatMessage(string $message, bool $plain = false)
    {
        return $message;
//        $regex = '/\$\{([a-z0-9:;-]+)\}/i';
//
//        // for plain mode we just strip all format information
//        return $plain ? preg_replace($regex, '', $message) :
//            preg_replace_callback($regex, function ($match) {
//                return $this->getEscapeSequence($match[1]);
//            }, $message) . $this->getEscapeSequence('r');
    }

//    /**
//     * Get the escape sequence(s) for $def
//     *
//     * $def can be anything that is defined static::$formats, just a foreground color name defined in static::$fgColors,
//     * or prefixed color name or number like `bg:cyan` or `fg:256`.
//     *
//     * In this function we don't test if the terminal supports a code. When the terminal does not support the code
//     * it is simply not used. So keep in mind that many terminals don't support dim, blink and hidden.
//     *
//     * @param string $def
//     * @return string
//     */
//    protected function getEscapeSequence(string $def): string
//    {
//        if (strpos($def, ';') !== false) {
//            return implode('', array_map([$this, 'getEscapeSequence'], explode(';', $def)));
//        }
//
//        if ('' === $def = trim($def)) {
//            return '';
//        }
//
//        if (isset(static::$formats[$def])) {
//            return $this->escape(static::$formats[$def]);
//        } elseif (substr($def, 0, 3) === 'fg:') {
//            if (is_numeric($color = substr($def, 3)) && $color <= 256) {
//                return $this->escape("38;5;" . $color);
//            } elseif (isset(static::$fgColors[$color])) {
//                return $this->escape(static::$fgColors[$color]);
//            }
//        } elseif (substr($def, 0, 3) === 'bg:') {
//            if (is_numeric($color = substr($def, 3)) && $color <= 256) {
//                return $this->escape("48;5;" . $color);
//            } elseif (isset(static::$bgColors[$color])) {
//                return $this->escape(static::$bgColors[$color]);
//            }
//        } elseif (isset(static::$fgColors[$def])) {
//            return $this->escape(static::$fgColors[$def]);
//        }
//
//        return '';
//    }
//
//    protected function escape(string $code): string
//    {
//        return sprintf("\033[%sm", $code);
//    }

    /**
     * Enable or disable logging
     *
     * @param bool $enabled
     */
//    public function logMessages(bool $enabled)
//    {
//        $this->logMessages = $enabled;
//    }

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
        $this->line('${red;bold}' . $message, self::WEIGHT_HIGHER);
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
}
