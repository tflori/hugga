<?php

namespace Hugga\Output\Drawing;

use Hugga\Console;
use Hugga\DrawingInterface;
use StringTemplate\AbstractEngine;
use StringTemplate\Engine;

class ProgressBar implements DrawingInterface
{
    /** Width of the progress bar (excl. other text)
     * @var int */
    protected $width = 50;

    /** Template to use
     * @var string */
    protected $template = '{title} {done}/{max} {type} [{progress}] {percentage}%';

    /** Characters to use [full, half, empty]
     * @var string[] */
    protected $symbols = ['#', '=', '-'];

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

    /**
     * ProgressBar constructor.
     *
     * @param float|int|null $max
     * @param string $title
     * @param string $type
     */
    public function __construct(?float $max, string $title = '', string $type = '')
    {
        $this->max = $max;
        $this->title = $title;
        $this->type = $type;
        $this->undetermined = $max <= 0;
        if ($this->undetermined) {
            $this->template = '{title} [{progress}]';
            $this->width = 20;
            $this->updateRate = 0.08;
        }
    }

    public function width(int $width)
    {
        $this->width = $width;
        return $this;
    }

    public function template(string $template, ?AbstractEngine $templateEngine = null)
    {
        $this->template = $template;
        if ($templateEngine) {
            $this->templateEngine;
        }
        return $this;
    }

    public function updateRate(float $updateRate)
    {
        $this->updateRate = $updateRate;
        return $this;
    }

    public function symbols(string $full = '#', string $half = '=', string $empty = '')
    {
        $this->symbols = [$full, $half, $empty];
        return $this;
    }

    public function undetermined()
    {
        $this->undetermined = true;
        return $this;
    }

    public function isUndetermined(): bool
    {
        return $this->undetermined;
    }

    public function start(Console $console, int $done = 0)
    {
        $this->console = $console;
        $this->done = $done;
        $console->addDrawing($this);
    }

    public function finish()
    {
        $this->done = $this->max;
        $this->console->removeDrawing($this);
    }

    public function progress(float $done, bool $flush = false)
    {
        if (!$this->isUndetermined()) {
            $this->done = $done;
            if ($this->done > $this->max) {
                $this->max = $this->done;
            }
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

    public function advance(float $steps = 1, bool $flush = false)
    {
        $this->progress($this->done + $steps);
    }

    public function getText(): string
    {
        if ($this->isUndetermined()) {
            return $this->getUndeterminedText();
        }

        $factor = $this->done / $this->max;
        $done = round($this->width * 2 * $factor);
        $full = ($done - $done % 2) / 2;
        $half = $done % 2;
        $empty = $this->width - $full - $half;
        $progress = str_repeat($this->symbols[0], $full) .
                    str_repeat($this->symbols[1], $half) .
                    str_repeat($this->symbols[2], $empty);

        return $this->getTemplateEngine()->render($this->template, [
            'title' => $this->title,
            'type' => $this->type,
            'done' => sprintf('% ' . strlen($this->max) . 'd', $this->done),
            'max' => $this->max,
            'progress' => $progress,
            'percentage' => sprintf('% 6.2f', $factor * 100),
        ]);
    }

    protected function getUndeterminedText(): string
    {
        if ($this->done === $this->max) {
            return $this->getTemplateEngine()->render('{title} ${green}done', [
                'title' => $this->title,
            ]);
        }

        $max = $this->width - 1;
        $pos = $max - abs(($this->done - 1) % ($max * 2) - $max);
        $progress = array_fill(0, $this->width, ' ');
        $progress[$pos] = $this->symbols[0];
        if ($pos > 0) {
            $progress[$pos-1] = $this->symbols[1];
            if ($pos > 1) {
                $progress[$pos-2] = $this->symbols[2];
            }
        }
        if ($pos < $this->width - 1) {
            $progress[$pos+1] = $this->symbols[1];
            if ($pos < $this->width - 2) {
                $progress[$pos+2] = $this->symbols[2];
            }
        }
        return $this->getTemplateEngine()->render($this->template, [
            'title' => $this->title,
            'progress' => implode('', $progress),
        ]);
    }

    protected function getTemplateEngine(): AbstractEngine
    {
        if (!$this->templateEngine) {
            $this->templateEngine = new Engine();
        }

        return $this->templateEngine;
    }
}
