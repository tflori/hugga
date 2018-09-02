<?php

namespace Hugga\Output;

use Hugga\Console;

class TtyHandler extends AbstractOutputHandler
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
        fwrite($this->resource, str_repeat("\e[D \e[D", $count));
    }

    public function deleteLine()
    {
        fwrite($this->resource, "\e[1K\r");
    }

    public function replace(string $new)
    {
        $lines = explode(PHP_EOL, $new);
        $this->write(str_repeat("\e[A", count($lines) - 1));
        foreach ($lines as $line) {
            $this->deleteLine();
            $this->console->write($line);
            $this->write("\e[B");
        }
    }
}
