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
    protected $useCursor = true;

    /** @var bool */
    protected $returnKey = false;

    /** @var int */
    protected $maxKeyLen = 1;

    /** @var mixed */
    protected $selected;

    /** @var int[] */
    protected $showedSlice = [0, 10];

    /** @var int */
    protected $maxVisible = 10;

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

        return $this->returnKey ? $key : $this->choices[$key] ?? null;
    }

    public function startInteractiveMode(Console $console)
    {
        // configure observer
        $observer = $console->getInputObserver();
        $values = array_keys($this->choices);

        // cursor up
        $observer->on("\e[A", function () use ($values, $console) {
            $pos = array_search($this->selected, $values);
            if ($pos > 0) {
                $this->selected = $values[$pos - 1];
                $this->updateSlice();
                $console->redraw();
            }
        });

        // cursor down
        $observer->on("\e[B", function () use ($values, $console) {
            $pos = array_search($this->selected, $values);
            if ($pos < count($values) - 1) {
                $this->selected = $values[$pos + 1];
                $this->updateSlice();
                $console->redraw();
            }
        });

        // confirm selection with enter
        $observer->on("\n", function () use ($observer) {
            $observer->stop();
        });

        // cancel selection with escape
        $observer->on("\e", function () use ($observer) {
            // reset the selected value
            if (!$this->default) {
                $this->selected = null;
            } elseif ($this->returnKey) {
                $this->default;
            } else {
                $this->selected = array_search($this->default, $this->choices);
            }

            $observer->stop();
        });

        // set selection to default value
        if (!$this->default) {
            $this->selected = reset($values);
        } elseif ($this->returnKey) {
            $this->selected = $this->default;
        } else {
            $this->selected = array_search($this->default, $this->choices);
        }
        $this->updateSlice();


            // run it
        $console->addDrawing($this);
        $observer->start();
        $selected = $this->selected;
        $this->selected = -1;
        $console->removeDrawing($this);
        return $selected;
    }

    protected function updateSlice()
    {
        $values = array_keys($this->choices);
        $pos = array_search($this->selected, $values);
        $this->showedSlice = [
            min(count($values) - $this->maxVisible, max(0, $pos - floor($this->maxVisible / 2))),
            $this->maxVisible
        ];
    }

    public function getText(): string
    {
        $text = $this->question ? $this->question . PHP_EOL : '';
        $text .= $this->formatChoices(array_slice($this->choices, ...$this->showedSlice));
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
