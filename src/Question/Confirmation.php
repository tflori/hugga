<?php

namespace Hugga\Question;

use Hugga\Console;
use Hugga\Question;

/**
 * Confirmation Question
 *
 * Supports changing the characters (currently only ascii supported).
 *
 * Example:
 * ```php
 * $confirmation = new Confirmation('Wollen Sie fortfahren?');
 * $confirmation->setCharacters('j', 'n')->ask($console);
 * ```
 *
 * Ideas:
 *   - support utf8 characters
 *   - support
 * @package Hugga\Question
 */
class Confirmation extends Question
{
    /** @var string */
    protected $true = 'y';

    /** @var string */
    protected $false = 'n';

    public function __construct(string $question, bool $default = false)
    {
        $this->question = $question;
        $this->default = $default;
    }

    /**
     * @param string $true
     * @param string $false
     * @return $this
     */
    public function setCharacters(string $true = 'y', string $false = 'n')
    {
        $this->true = strtolower($true[0]);
        $this->false = strtolower($false[0]);
        return $this;
    }

    /**
     * @param Console $console
     * @return bool|string
     */
    public function ask(Console $console)
    {
        $console->write($this->getQuestionText(), Console::WEIGHT_HIGH);
        $answer = $console->waitChars();
        $answer === substr(PHP_EOL, 0, 1) || $console->line('');
        $answer = strtolower(trim($answer));
        if (empty($answer) || !in_array($answer, [$this->true, $this->false])) {
            return $this->default;
        }

        return $answer === $this->true ? true : false;
    }

    protected function getQuestionText(): string
    {
        return sprintf(
            '%s [ %s / %s ] ',
            $this->question,
            $this->default ? strtoupper($this->true) : $this->true,
            $this->default ? $this->false : strtoupper($this->false)
        );
    }
}
