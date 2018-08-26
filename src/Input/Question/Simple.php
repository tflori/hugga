<?php

namespace Hugga\Input\Question;

use Hugga\Console;

class Simple extends AbstractQuestion
{
    /** @var bool */
    protected $required = false;

    public function ask(Console $console)
    {
        $console->write($this->getQuestionText(), Console::WEIGHT_HIGH);
        $answer = trim($console->readLine());
        return $answer === '' ? $this->default : $answer;
    }
}
