<?php

namespace Hugga\Output;

class TtyHandler extends AbstractOutputHandler
{
    public static function isCompatible($resource)
    {
        return parent::isCompatible($resource) && static::isTty($resource);
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
}
