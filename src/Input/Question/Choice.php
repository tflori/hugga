<?php

namespace Hugga\Input\Question;

use Hugga\Console;
use Hugga\DrawingInterface;
use Hugga\Input\Observer;

class Choice extends AbstractQuestion implements DrawingInterface
{
    /** @var array */
    protected $choices;

    /** @var bool */
    protected $returnKey = false;

    /** @var bool */
    protected $useCursor = true;

    /** @var mixed */
    protected $selected;

    protected $maxKeyLen = 1;

    public function __construct(array $choices, string $question = '', $default = null)
    {
        $this->choices = $this->prepareChoices($choices);
        $this->returnKey = !$this->isIndexedArray($choices);
        $this->maxKeyLen = max(array_map('strlen', array_keys($this->choices)));
        parent::__construct($question, $default);
    }

    public function ask(Console $console)
    {
        if ($this->useCursor && Observer::isCompatible($console->getInput())) {
            $key = $this->startInteractiveMode($console);
        } else {
            $console->line($this->question, Console::WEIGHT_HIGH);
            $console->line($this->formatChoices($this->choices), Console::WEIGHT_HIGH);

            $key = $console->readLine('> ');
            if (empty($key)) {
                $key = $this->returnKey ? $this->default : array_search($this->default, $this->choices);
            }
            while (!isset($this->choices[$key])) {
                $console->line('${red}Unknown choice ' . $key, Console::WEIGHT_HIGH);
                $console->line($this->question, Console::WEIGHT_HIGH);
                $console->line($this->formatChoices($this->choices), Console::WEIGHT_HIGH);
                $key = $console->readLine('> ');
            }
        }

        return $this->returnKey ? $key : $this->choices[$key];
    }

    public function startInteractiveMode(Console $console)
    {
        $console->addDrawing($this);
        $observer = $console->getInputObserver();
        $values = array_keys($this->choices);
        if (!$this->default) {
            $this->selected = reset($values);
        } elseif ($this->returnKey) {
            $this->selected = $this->default;
        } else {
            $this->selected = array_search($this->default, $this->choices);
        }

        // cursor up
        $observer->on("\e[A", function ($event) use ($values, $console) {
            $pos = array_search($this->selected, $values);
            if ($pos > 0) {
                $this->selected = $values[$pos - 1];
                $console->redraw();
            }
        });
        // cursor down
        $observer->on("\e[B", function ($event) use ($values, $console) {
            $pos = array_search($this->selected, $values);
            if ($pos < count($values) - 1) {
                $this->selected = $values[$pos + 1];
                $console->redraw();
            }
        });

        $observer->on("\n", function () use ($observer) {
            $observer->stop();
        });

        $observer->start();
        $console->removeDrawing($this);
        return $this->selected;
    }

    public function getText(): string
    {
        $text = $this->question ? $this->question . PHP_EOL : '';
        $text .= $this->formatChoices($this->choices);
        return $text;
    }

    protected function formatChoices($choices)
    {
        return implode(PHP_EOL, array_map(
            [$this, 'formatChoice'],
            array_keys($choices),
            array_values($choices)
        ));
    }

    protected function formatChoice($value, $text)
    {
        $choice = sprintf('% ' . ($this->maxKeyLen + 2) . 's %s', '[' . $value . ']', $text);

        if ($this->selected && $this->selected === $value ||
            !$this->selected && $this->returnKey && $this->default === $value ||
            !$this->selected && !$this->returnKey && $this->default === $text) {
            $choice = '${invert}' . $choice . '${r}';
        }

        return '  ' . $choice;
    }

    protected function isIndexedArray(iterable $array)
    {
        return isset($array[0]) && (isset($array[1]) || count($array) === 1);
    }

    /**
     * Make an indexed array more readable for humans
     *
     * Replaces keys from indexed arrays from 1 to 9 or a to z if it fits.
     *
     * @param array $choices
     * @return array
     */
    protected function prepareChoices(array $choices): array
    {
        if ($this->isIndexedArray(($choices))) {
            // we have an indexed array
            if (count($choices) < 10) {
                // keys from 1 - 9
                return array_combine(range(1, count($choices)), array_values($choices));
            } elseif (count($choices) < 27) {
                // keys from a - z
                return array_combine(
                    range('a', chr(ord('a') + count($choices) - 1)),
                    array_values($choices)
                );
            }
        }

        return $choices;
    }
}
