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

    public function delete(int $chars)
    {
        $left = chr(0x1b) . chr(0x5b) . chr(0x44);
        fwrite($this->resource, str_repeat($left . ' ' . $left, $chars));
    }

    public function deleteLine()
    {
        fwrite($this->resource, "\e[1K\r");
    }
}
