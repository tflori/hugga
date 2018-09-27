<?php

namespace Hugga\Input\Question;

use Hugga\Console;
use Hugga\DrawingInterface;
use Hugga\Input\Observer;
use Hugga\InteractiveOutputInterface;

class Choice extends AbstractQuestion implements DrawingInterface
{
    /** @var array */
    protected $choices;

    /** @var bool */
    protected $indexedArray;

    /** @var bool */
    protected $interactive = true;

    /** @var bool */
    protected $returnKey = false;

    /** @var int */
    protected $maxKeyLen = 1;

    /** @var mixed */
    protected $selected;

    /** @var int */
    protected $offset = 0;

    /** @var int */
    protected $maxVisible = 0;

    public function __construct(array $choices, string $question = '', $default = null)
    {
        $this->indexedArray = $this->isIndexedArray($choices);
        $this->choices = $this->indexedArray ? $this->prepareChoices($choices) : $choices;
        $this->returnKey = !$this->indexedArray;
        $this->maxKeyLen = max(array_map('strlen', array_keys($this->choices)));
        parent::__construct($question, $default);
    }

    /**
     * Show at max $count choices in interactive mode
     *
     * @param int $count
     * @return $this
     */
    public function limit(int $count)
    {
        $this->maxVisible = $count;
        return $this;
    }

    /**
     * Don't use interactive mode
     *
     * @return $this
     */
    public function nonInteractive()
    {
        $this->interactive = false;
        return $this;
    }

    /**
     * Return keys even if the choices have sequential keys
     *
     * @return $this
     */
    public function returnKey()
    {
        $this->returnKey = true;
        return $this;
    }

    /**
     * Return value even if the choices have alphanumeric keys
     *
     * @return $this
     */
    public function returnValue()
    {
        $this->returnKey = false;
        return $this;
    }

    public function ask(Console $console)
    {
        if ($this->interactive && $console->isInteractive() && Observer::isCompatible($console->getInput())) {
            /** @var InteractiveOutputInterface $output */
            $output = $console->getOutput();
            $maxVisible = $output->getSize()[0] - 2;
            if (!$this->maxVisible || $maxVisible < $this->maxVisible) {
                $this->maxVisible = $maxVisible;
            }
            $key = $this->startInteractiveMode($console);
        } else {
            $console->line($this->question, Console::WEIGHT_HIGH);
            $console->line($this->formatChoices($this->choices), Console::WEIGHT_HIGH);

            $key = trim($console->readLine('> '));
            while (!empty($key) && !isset($this->choices[$key])) {
                $console->line('${red}Unknown choice ' . $key, Console::WEIGHT_HIGH);
                $console->line($this->question, Console::WEIGHT_HIGH);
                $console->line($this->formatChoices($this->choices), Console::WEIGHT_HIGH);
                $key = $console->readLine('> ');
            }
            if (empty($key)) {
                $key = $this->returnKey ? $this->default : array_search($this->default, $this->choices);
            }
        }

        if ($this->indexedArray && $this->returnKey) {
            return is_numeric($key) ? $key - 1 : $this->charsToIndex($key);
        }
        return $this->returnKey ? $key : $this->choices[$key] ?? null;
    }

    /**
     * @param Console $console
     * @return int|string
     */
    public function startInteractiveMode(Console $console)
    {
        // configure observer
        $observer = $console->getInputObserver();
        $values = array_keys($this->choices);

        // cursor up
        $observer->on("\e[A", function () use ($values, $console) {
            $this->changePos($values, $console, -1);
        });
        // cursor down
        $observer->on("\e[B", function () use ($values, $console) {
            $this->changePos($values, $console, +1);
        });
        // page up
        $observer->on("\e[5~", function () use ($values, $console) {
            $this->changePos($values, $console, -$this->maxVisible/2);
        });
        // page down
        $observer->on("\e[6~", function () use ($values, $console) {
            $this->changePos($values, $console, +$this->maxVisible/2);
        });
        // home
        $observer->on("\e[H", function () use ($values, $console) {
            $this->changePos($values, $console, -count($values));
        });
        // end
        $observer->on("\e[F", function () use ($values, $console) {
            $this->changePos($values, $console, +count($values));
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

    protected function changePos(array $values, Console $console, int $change)
    {
        $pos = array_search($this->selected, $values);
        $newPos = min(count($values)-1, max(0, $pos + $change));
        if ($pos != $newPos) {
            $this->selected = $values[$newPos];
            $this->updateSlice();
            $console->redraw();
        }
    }

    protected function updateSlice()
    {
        $values = array_keys($this->choices);
        $pos = array_search($this->selected, $values);
        $this->offset = max(0, min(count($values) - $this->maxVisible, $pos - floor($this->maxVisible / 2)));
    }

    public function getText(): string
    {
        $text = $this->question ? $this->question . PHP_EOL : '';
        $slice = array_slice($this->choices, $this->offset, $this->maxVisible, true);
        $text .= $this->formatChoices($slice);
        if (count($this->choices) > $this->maxVisible) {
            $text .= PHP_EOL .
                     'Showing ' . $this->offset . ' - ' . ($this->offset + $this->maxVisible) .
                     ' out of ' . count($this->choices);
        }
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

        if ($this->selected !== null && $this->selected === $value ||
            !$this->selected && $this->returnKey && $this->default === $value ||
            !$this->selected && !$this->returnKey && $this->default === $text) {
            $choice = '${invert}' . $choice . '${r}';
        }

        return '  ' . $choice;
    }

    /**
     * Determines if the array is indexed (starts from 0)
     *
     * @param array $array
     * @return bool
     */
    protected function isIndexedArray(array $array)
    {
        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * Make an indexed array more readable for humans
     *
     * Replaces keys from indexed arrays from 1 to 9 or a to zz.
     *
     * @param array $choices
     * @return array
     */
    protected function prepareChoices(array $choices): array
    {
        if (count($choices) < 10) {
            // keys from 1 - 9
            return array_combine(range(1, count($choices)), array_values($choices));
        }

        $keys = array_map([$this, 'indexToChars'], range(0, count($choices)-1));
        return array_combine($keys, $choices);
    }

    /**
     * Converts an index to a - zz
     *
     * @param int $i
     * @return string
     */
    protected function indexToChars($i)
    {
        $c = '';
        do {
            $r = $i % 26;
            $c = chr(97 + $r) . $c;
            $i = ($i - $r) / 26 -1;
        } while ($i > -1);
        return $c;
    }

    /**
     * Converts a - zz to index
     *
     * @param string $c
     * @return int
     */
    protected function charsToIndex($c)
    {
        $i = ord(substr($c, -1)) - 97;
        $c = substr($c, 0, -1);
        while (strlen($c)) {
            $i += (ord(substr($c, -1)) - 96) * 26;
            $c = substr($c, 0, -1);
        }
        return $i;
    }
}
