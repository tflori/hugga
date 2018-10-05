<?php

namespace Hugga\Input;

class ObserverFaker extends Observer
{
    protected $keyPresses = [];

    /**
     * Fake start listening
     *
     * Sending keys will not have effect until started.
     */
    public function start(): void
    {
        $this->stop = false;
        while (!$this->stop && $char = array_shift($this->keyPresses)) {
            $this->handle($char);
        }
    }

    /**
     * Fake sending key presses after start()
     *
     * @param string[] $chars
     */
    public function sendKeys(string ...$chars)
    {
        array_push($this->keyPresses, ...$chars);
    }
}
