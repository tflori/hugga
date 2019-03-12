<?php

namespace Hugga\Output\Drawing;

use Hugga\Console;
use Hugga\DrawingInterface;
use StringTemplate\AbstractEngine;
use StringTemplate\SprintfEngine;

class ProgressBar implements DrawingInterface
{
    protected static $formatDefinitions = [
        'percentage' => '{percentage%3.0f}%',
        'steps' => '{done}/{max}',
        'steps-with-type' => '{done}/{max} {type}',

        'basic' => '{steps} |{progress}| {percentage}',
        'basic-with-title' => '{title} {steps} |{progress}| {percentage}',

        'undetermined' => '|{progress}|',
        'undetermined-with-title' => '{title} |{progress}|',
    ];

    /** Characters to use [empty, full, half] or [empty, full, one third, two thirds]...
     * @var string[] */
    protected static $defaultProgressCharacters = [' ', '█', '▏', '▎', '▍', '▌', '▋', '▊', '▉'];

    /** Throbber for undetermined progress bar
     * @var string */
    protected static $defaultThrobber = '░▒▓█▓▒░';

    /** Width of the progress bar (excl. other text)
     * @var int */
    protected $width = 50;

    /** Template to use
     * @var string */
    protected $template = 'basic';

    /** @var float|int */
    protected $max = 50;

    /** @var int */
    protected $done = 0;

    /** @var AbstractEngine */
    protected $templateEngine;

    /** @var string */
    protected $title = '';

    /** @var string */
    protected $type = '';

    /** @var bool */
    protected $undetermined = false;

    /** @var Console */
    protected $console;

    /** @var float */
    protected $lastFlush;

    /** Update every x seconds
     * @var float  */
    protected $updateRate = 0.04; // 25 frames per second

    /** @var string[] */
    protected $progressCharacters = [];

    /** @var string  */
    protected $throbber = '';

    /**
     * Change or add format definitions
     *
     * @param string $name
     * @param string $format
     */
    public static function setFormatDefinition(string $name, string $format)
    {
        self::$formatDefinitions[$name] = $format;
    }

    /**
     * Change the default progress characters
     *
     * @param string $empty
     * @param string $full
     * @param string ...$steps
     */
    public static function setDefaultProgressCharacters(string $empty, string $full, string ...$steps)
    {
        self::$defaultProgressCharacters = [];
        array_push(self::$defaultProgressCharacters, $empty, $full, ...$steps);
    }

    /**
     * Reset the default progress characters
     */
    public static function resetDefaultProgressCharacters()
    {
        self::$defaultProgressCharacters = [' ', '█', '▏', '▎', '▍', '▌', '▋', '▊', '▉'];
    }

    /**
     * Change the default throbber
     *
     * @param string $throbber
     */
    public static function setDefaultThrobber(string $throbber)
    {
        self::$defaultThrobber = $throbber;
    }

    /**
     * Reset the default throbber
     */
    public static function resetDefaultThrobber()
    {
        self::$defaultThrobber = '░▒▓█▓▒░';
    }

    /**
     * ProgressBar constructor.
     *
     * @param Console $console
     * @param float|int|null $max
     * @param string $title
     * @param string $type
     */
    public function __construct(Console $console, $max, string $title = '', string $type = '')
    {
        $this->console = $console;
        $this->max = $max;
        $this->title = $title;
        $this->type = $type;
        $this->undetermined = $max <= 0;
        $this->width = is_int($max) ? min($this->width, $max) : $this->width;
        $this->progressCharacters = self::$defaultProgressCharacters;
        $this->throbber = self::$defaultThrobber;

        if ($this->undetermined) {
            $this->template = 'undetermined';
            $this->width = 20;
            $this->updateRate = 0.08;
        }

        if (!empty($this->title)) {
            $this->template .= '-with-title';
        }
    }

    public function width(int $width)
    {
        $this->width = $width;
        return $this;
    }

    public function template(string $template)
    {
        $this->template = $template;
        return $this;
    }

    public function updateRate(float $updateRate)
    {
        $this->updateRate = $updateRate;
        return $this;
    }

    public function undetermined()
    {
        $this->undetermined = true;
        $this->template = 'undetermined';
        $this->width = 20;
        return $this;
    }

    public function progressCharacters(string $empty, string $full, string ...$steps)
    {
        $this->progressCharacters = [];
        array_push($this->progressCharacters, $empty, $full, ...$steps);
        return $this;
    }

    public function throbber(string $throbber)
    {
        $this->throbber = $throbber;
        return $this;
    }

    public function isUndetermined(): bool
    {
        return $this->undetermined;
    }

    public function start($done = 0)
    {
        $this->lastFlush = microtime(true);
        $this->done = $done;
        if (!$this->console->addDrawing($this)) {
            $this->console->redraw();
        }
    }

    public function finish()
    {
        $this->done = $this->max;
        $this->console->removeDrawing($this);
    }

    public function progress($done, bool $flush = false)
    {
        if (!$this->isUndetermined()) {
            $this->done = min($done, $this->max);
        }

        $now = microtime(true);
        $flush = $flush ?: $now - $this->lastFlush > $this->updateRate;
        if ($flush) {
            if ($this->isUndetermined()) {
                $this->done++;
            }
            $this->lastFlush = $now;
            $this->console->redraw();
        }
    }

    public function advance($steps = 1)
    {
        $this->progress($this->done + $steps);
    }

    public function getText(): string
    {
        $engine = $this->getTemplateEngine();
        $title = $this->title;
        $percentage = '';
        $steps = '';

        if (!$this->isUndetermined()) {
            $stepsTemplate = $this->type ? 'steps-with-type' : 'steps';
            $steps = $engine->render(static::$formatDefinitions[$stepsTemplate], [
                'done' => sprintf($this->getMaxFormat(), $this->done),
                'max' => $this->max,
                'type' => $this->type,
            ]);
            $percentage = $engine->render(static::$formatDefinitions['percentage'], [
                'percentage' => $this->done / $this->max * 100,
            ]);
        }

        if ($this->done === $this->max) {
            $progress = str_repeat($this->progressCharacters[1], $this->width);
            return $engine->render($this->getTemplate(), compact('title', 'progress', 'steps', 'percentage'));
        }

        $progress = $this->isUndetermined() ? $this->getUndeterminedProgress() : $this->getProgress();
        return $engine->render($this->getTemplate(), compact('title', 'progress', 'steps', 'percentage'));
    }

    protected function getMaxFormat()
    {
        return is_int($this->max) ?
            '%' . strlen($this->max) . 'd' :
            '%' . (strlen($this->max) + 3) . '.2f';
    }

    protected function getProgress(): string
    {
        $factor = $this->done / $this->max;
        $steps = count($this->progressCharacters) - 1;
        $done = floor($this->width * $steps * $factor);
        $full = ($done - $done % $steps) / $steps;
        $half = $done % $steps > 0 ? 1 : 0;
        $empty = max(0, $this->width - $full - $half);
        return str_repeat($this->progressCharacters[1], $full) .
               str_repeat($this->progressCharacters[($done % $steps) + 1], $half) .
               str_repeat($this->progressCharacters[0], $empty);
    }

    protected function getUndeterminedProgress(): string
    {
        $lenIndicator = mb_strlen($this->throbber);
        $halfLength = intval($lenIndicator / 2);
        $center = $lenIndicator % 2 ? mb_substr($this->throbber, $halfLength, 1) : '';
        $left = $lenIndicator > 1 ? mb_substr($this->throbber, 0, $halfLength) : '';
        $right = $lenIndicator > 1 ? mb_substr($this->throbber, -$halfLength) : '';

        $max = $this->width - (empty($center) ? 0 : 1);
        $pos = $max - abs(($this->done) % ($max * 2) - $max);

        $progress = '';
        if ($pos > 0) {
            $progress .= str_repeat($this->progressCharacters[0], max(0, $pos - mb_strlen($left)));
            $progress .= mb_substr($left, -$pos);
        }
        $progress .= $center;
        $rest = $this->width - mb_strlen($progress);
        if ($rest > 0) {
            $progress .= mb_substr($right, 0, $rest);
            $progress .= str_repeat($this->progressCharacters[0], max(0, $rest - mb_strlen($right)));
        }

        return $progress;
    }

    protected function getTemplateEngine(): AbstractEngine
    {
        if (!$this->templateEngine) {
            $this->templateEngine = new SprintfEngine();
        }

        return $this->templateEngine;
    }

    protected function getTemplate()
    {
        return strpos($this->template, '{progress}') === false && isset(static::$formatDefinitions[$this->template]) ?
            static::$formatDefinitions[$this->template] :
            $this->template;
    }
}
