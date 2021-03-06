<?php

namespace Hugga\Input;

use Hugga\Console;
use Hugga\InputInterface;

/**
 * Class Observer
 *
 * @package Hugga
 * @author Thomas Flori <thflori@gmail.com>
 * @codeCoverageIgnore not testable
 */
class Observer
{
    protected $stdin;

    protected $charHandler = [];

    protected $handler = [];

    protected $stop = true;

    public static function isCompatible(InputInterface $input)
    {
        exec('which stty', $dummy, $returnVar);
        return $returnVar === 0 && Console::isTty($input->getResource());
    }

    public function __construct(InputInterface $input)
    {
        $this->stdin = $input->getResource();
    }

    public function start(): void
    {
        $this->stop = false;

        // change tty settings
        $sttySettings = $this->ttySettings('-g');
        self::ttySettings('sane cbreak -echo');

        while (!$this->stop) {
            $c = '';
            do {
                $c .= fgetc($this->stdin);
                $input = [$this->stdin];
            } while (stream_select($input, $output, $error, 0) && in_array($this->stdin, $input));

            $this->handle($c);
        }

        // reset tty settings
        self::ttySettings($sttySettings);
    }

    public function stop(): void
    {
        $this->stop = true;
    }

    protected function handle(string $char)
    {
        $event = (object)[
            'type' => 'keyUp',
            'char' => $char,
            'stopPropagation' => false,
        ];

        $handlers = array_reverse($this->handler);
        if (isset($this->charHandler[$char])) {
            array_unshift($handlers, ...array_reverse($this->charHandler[$char]));
        }
        foreach ($handlers as $handler) {
            $handler($event);
            if ($event->stopPropagation) {
                return;
            }
        }
    }

    public function on(string $char, callable $callback)
    {
        if (!isset($this->charHandler[$char])) {
            $this->charHandler[$char] = [];
        }

        $this->charHandler[$char][] = $callback;
        return $this;
    }

    public function addHandler(callable $callback)
    {
        $this->handler[] = $callback;
        return $this;
    }

    public function off(string $char, callable $callback)
    {
        if (!isset($this->charHandler[$char])) {
            return $this;
        }

        if ($pos = array_search($callback, $this->charHandler[$char])) {
            array_splice($this->charHandler[$char], $pos, 1);
        }
        return $this;
    }

    public function removeHandler(callable $callback)
    {
        if ($pos = array_search($callback, $this->charHandler[$char])) {
            array_splice($this->charHandler[$char], $pos, 1);
        }
        return $this;
    }

    public static function ttySettings($options)
    {
        exec($cmd = "stty $options", $output, $returnValue);
        if ($returnValue) {
            throw new \Exception('Failed to change tty settings');
        }
        return implode(' ', $output);
    }
}
