<?php

namespace Hugga\Input\Question;

use Hugga\Console;

abstract class AbstractQuestion
{
    /** @var string */
    protected $question;

    /** @var mixed */
    protected $default;

    /**
     * @param string $question
     * @param string $default
     */
    public function __construct(string $question = '', $default = null)
    {
        $this->question = $question;
        $this->default = $default;
    }

    abstract public function ask(Console $console);

    protected function getQuestionText(): string
    {
        if (empty($this->question)) {
            return '';
        }

        return sprintf(
            $this->default ? '%s [%s] ' : '%s ',
            $this->question,
            $this->default
        );
    }
}
