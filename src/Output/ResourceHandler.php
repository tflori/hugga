<?php

namespace Hugga\Output;

class ResourceHandler extends AbstractOutputHandler
{
    const BUFFER_SIZE = 4096;

    public function write(string $str)
    {
        fwrite($this->resource, $str);
    }

    public function delete(int $chars)
    {
        $stat = fstat($this->resource);
        ftruncate($this->resource, $stat['size'] - $chars);
        fseek($this->resource, 0, SEEK_END);
    }

    public function deleteLine()
    {
        do {
            // read the tail of the file
            $stat = fstat($this->resource);
            $i = max(0, $stat['size'] - self::BUFFER_SIZE);
            fseek($this->resource, $i);
            $tail = fread($this->resource, self::BUFFER_SIZE);

            if (false !== $pos = strrpos($tail, PHP_EOL)) {
                $this->delete(strlen($tail) - $pos - 1);
                return;
            } else {
                $this->delete(strlen($tail));
            }
        } while($i > 0);
    }
}
