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
}
