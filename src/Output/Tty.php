<?php

namespace Hugga\Output;

use Hugga\Console;
use Hugga\InteractiveOutputInterface;

class Tty extends AbstractOutput implements InteractiveOutputInterface
{
    public static function isCompatible($resource): bool
    {
        return parent::isCompatible($resource) && Console::isTty($resource);
    }

    public function write(string $str)
    {
        fwrite($this->resource, $str);
    }

    public function delete(int $count)
    {
        $this->write(str_repeat("\e[D \e[D", $count));
    }

    public function deleteLine()
    {
        $this->write("\e[2K\r");
    }

    /** {@inheritdoc}
     * @codeCoverageIgnore we can not test this with phpunit
     */
    public function replace(string $new)
    {
        $lines = explode(PHP_EOL, $new);
        $this->write(str_repeat("\e[A", count($lines)));
        foreach ($lines as $line) {
            $this->write("\e[B");
            $this->deleteLine();
            $this->write($line);
        }
    }

    /** {@inheritdoc}
     * @codeCoverageIgnore we can not test this with phpunit
     */
    public function deleteLines(int $count)
    {
        $this->deleteLine();
        for ($i = 1; $i < $count; $i++) {
            $this->write("\e[A");
            $this->deleteLine();
        }
    }

    /**
     * Get the size of the output window
     *
     * Returns an array with [int $rows, int $cols]
     *
     * @return array
     * @codeCoverageIgnore unable to test
     */
    public function getSize(): array
    {
        exec('which stty', $dummy, $returnVar);
        if ($returnVar === 0) {
            return array_map('intval', explode(' ', exec('stty size')));
        }

        exec('which tput', $dummy, $returnVar);
        if ($returnVar === 0) {
            $rows = (int)exec('tput rows');
            $cols = (int)exec('tput cols');
            return [$rows, $cols];
        }

        // fallback 20 rows, 80 columns
        return [(int)getenv('LINES') ?: 20, (int)getenv('COLUMNS') ?: 80];
    }
}
