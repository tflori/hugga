<?php

namespace Hugga\Question;

use Hugga\Console;
use Hugga\Question;

class Confirmation extends Question
{
    public function __construct(string $question, bool $default = false)
    {
        $this->question = $question;
        $this->default = $default;
    }

    public function ask(Console $console)
    {
        $console->write($this->getQuestionText());
        $answer = $console->waitChars();
        $answer === substr(PHP_EOL, 0, 1) || $console->line('');
        $answer = strtolower(trim($answer));
        if (empty($answer)) {
            return $this->default;
        }

        if ($this->default && $answer !== 'n') {
            return true;
        } elseif (!$this->default && $answer !== 'y') {
            return false;
        }

        return $answer === 'y' ? true : false;
    }

    protected function getQuestionText(): string
    {
        return sprintf(
            '%s [ %s / %s ] ',
            $this->question,
            $this->default ? 'Y' : 'y',
            $this->default ? 'n' : 'N'
        );
    }
}
