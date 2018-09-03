<?php

namespace Hugga\Output;

class File extends AbstractOutput
{
    const BUFFER_SIZE = 4096;

    public function write(string $str)
    {
        fwrite($this->resource, $str);
    }

    public function delete(int $count)
    {
        $i = max(0, fstat($this->resource)['size'] - $count * 4);
        fseek($this->resource, $i);
        $str = mb_substr(fread($this->resource, $count * 4), -$count);
        $this->truncate(strlen($str));
    }

    public function deleteLine(int $bufferSize = self::BUFFER_SIZE)
    {
        do {
            // read the tail of the file
            $stat = fstat($this->resource);
            $i = max(0, $stat['size'] - $bufferSize);
            fseek($this->resource, $i);
            $tail = fread($this->resource, $bufferSize);

            if (false !== $pos = strrpos($tail, PHP_EOL)) {
                $this->truncate(strlen($tail) - $pos - 1);
                return;
            } else {
                $this->truncate(strlen($tail));
            }
        } while ($i > 0);
    }

    protected function truncate(int $count)
    {
        $stat = fstat($this->resource);
        ftruncate($this->resource, $stat['size'] - $count);
        fseek($this->resource, 0, SEEK_END);
    }
}
