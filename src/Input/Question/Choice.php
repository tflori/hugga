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
        $this->choices = $choices;
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
        if ($this->interactive && $console->isInteractive() && $observer = $console->getInputObserver()) {
            $key = $this->askInteractive($console, $observer);
        } else {
            $this->interactive = false;
            $key = $this->askNonInteractive($console);
        }

        return $this->returnKey ? $key : $this->choices[$key] ?? null;
    }

    /**
     * Starts the interactive question
     *
     * @param Console $console
     * @param Observer $observer
     * @return int|string
     */
    protected function askInteractive(Console $console, Observer $observer)
    {
        /** @var InteractiveOutputInterface $output */
        $output = $console->getOutput();
        $maxVisible = $output->getSize()[0] - 2;
        if (!$this->maxVisible || $maxVisible < $this->maxVisible) {
            $this->maxVisible = $maxVisible;
        }
        $values = array_keys($this->choices);

        // cursor up
        $observer->on("\e[A", function () use ($values, $console) {
            $this->changePos($values, $console, -1, true);
        });
        // cursor down
        $observer->on("\e[B", function () use ($values, $console) {
            $this->changePos($values, $console, +1, true);
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
        $console->removeDrawing($this);
        return $selected;
    }

    /**
     * Change selection by $change
     *
     * @param array $values
     * @param Console $console
     * @param int $change
     * @param bool $loop
     */
    protected function changePos(array $values, Console $console, int $change, bool $loop = false)
    {
        $pos = array_search($this->selected, $values);
        $last = count($values) - 1;
        $newPos = min($last, max(0, $pos + $change));
        if ($newPos === 0 && $pos === 0 && $loop) {
            $newPos = $last;
        } elseif ($newPos === $last && $pos === $last && $loop) {
            $newPos = 0;
        }
        if ($pos != $newPos) {
            $this->selected = $values[$newPos];
            $this->updateSlice();
            $console->redraw();
        }
    }

    /**
     * Update the offset based on $maxVisible and
     */
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

    /**
     * Format $choices as rows
     *
     * @param $choices
     * @return string
     */
    protected function formatChoices($choices)
    {
        return implode(PHP_EOL, array_map(
            function ($key, $value) {
                return $this->formatChoice($key, $value, $this->isSelected($key, $value));
            },
            array_keys($choices),
            array_values($choices)
        ));
    }

    /**
     * Format the choice
     *
     * Overload for different formatting.
     *
     * @param string|int $key
     * @param string $value
     * @param bool $selected
     * @return string
     */
    protected function formatChoice($key, string $value, bool $selected = false): string
    {
        $choice = $value;

        if ($this->returnKey || !$this->interactive) {
            $choice = sprintf('% ' . ($this->maxKeyLen + 2) . 's %s', '[' . $key . ']', $value);
        }

        if ($selected) {
            $choice = '${invert}' . $choice . '${r}';
        }

        return '  ' . $choice;
    }

    /**
     * Check if $key => $value pair is selected
     *
     * @param $key
     * @param string $value
     * @return bool
     */
    protected function isSelected($key, string $value): bool
    {
        if ($this->interactive) {
            return $this->selected === $key;
        }

        if ($this->returnKey) {
            return $this->default === $key;
        }

        return $this->default === $value;
    }

    /**
     * Starts the non interactive question
     *
     * @param Console $console
     * @return false|int|mixed|null|string
     */
    protected function askNonInteractive(Console $console)
    {
        if ($this->indexedArray) {
            $this->humanizeKeys();
        }

        $key = $this->writeQuestionAndWaitAnswer($console);
        // ask till we have a valid answer
        while (!empty($key) && !isset($this->choices[$key])) {
            // get the key if the answer is the value
            if ($valueKey = array_search($key, $this->choices)) {
                $key = $valueKey;
                break;
            }
            $console->line('${red}Unknown choice ' . $key, Console::WEIGHT_HIGH);
            $key = $this->writeQuestionAndWaitAnswer($console);
        }

        // use the default without answer
        if (empty($key)) {
            $key = $this->returnKey ? $this->default : array_search($this->default, $this->choices);
        }

        // dehumanize the key if it should be returned
        if (!empty($key) && $this->indexedArray && $this->returnKey) {
            return is_numeric($key) ? $key - 1 : $this->charsToIndex($key);
        }

        return $key;
    }

    /**
     * @param Console $console
     * @return string
     */
    protected function writeQuestionAndWaitAnswer(Console $console): string
    {
        if ($this->question) {
            $console->line($this->question, Console::WEIGHT_HIGH);
        }
        $console->line($this->formatChoices($this->choices), Console::WEIGHT_HIGH);
        return trim($console->readLine('> '));
    }

    /**
     * Make an indexed array more readable for humans
     *
     * Replaces keys from indexed arrays from 1 to 9 or a to zz.
     */
    protected function humanizeKeys()
    {
        if (count($this->choices) < 10) {
            // keys from 1 - 9
            $this->choices =  array_combine(range(1, count($this->choices)), array_values($this->choices));

            if ($this->returnKey && $this->default !== null) {
                $this->default += 1;
            }

            return;
        }

        $keys = array_map([$this, 'indexToChars'], range(0, count($this->choices)-1));
        $this->choices = array_combine($keys, $this->choices);

        if ($this->returnKey && $this->default !== null) {
            $this->default = $this->indexToChars($this->default);
        }
    }

    /**
     * Converts an index to a - zz
     *
     * @param int $i
     * @return string
     */
    protected static function indexToChars($i)
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
    protected static function charsToIndex($c)
    {
        $i = ord(substr($c, -1)) - 97;
        $c = substr($c, 0, -1);
        while (strlen($c)) {
            $i += (ord(substr($c, -1)) - 96) * 26;
            $c = substr($c, 0, -1);
        }
        return $i;
    }

    /**
     * Determines if the array is indexed (starts from 0)
     *
     * @param array $array
     * @return bool
     */
    protected static function isIndexedArray(array $array)
    {
        return array_keys($array) === range(0, count($array) - 1);
    }
}
