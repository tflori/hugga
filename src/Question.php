<?php

namespace Hugga;

class Question
{
    /** @var string */
    protected $question;

    /** @var string */
    protected $default;

    /** @var bool */
    protected $required = false;

    /**
     * Question constructor.
     * @param string $question
     * @param string $default
     */
    public function __construct(string $question = '', string $default = null)
    {
        $this->question = $question;
        $this->default = $default;
    }

    public function ask(Console $console)
    {
        $console->write($this->getQuestionText(), Console::WEIGHT_HIGH);
        $answer = trim($console->waitLine());
        return $answer === '' ? $this->default : $answer;
    }

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
