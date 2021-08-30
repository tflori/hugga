<?php

namespace Hugga\Output;

use Hugga\Console;

class File extends AbstractOutput
{
    const BUFFER_SIZE = 4096;

    protected $written = '';

    protected $seekable = false;

    public function __construct(Console $console, $resource)
    {
        parent::__construct($console, $resource);
        // disable ansi by default
        $this->console->disableAnsi();
        $this->seekable = stream_get_meta_data($resource)['seekable'];
    }


    public function write(string $str)
    {
        fwrite($this->resource, $str);
        !$this->seekable || $this->written = substr($this->written . $str, 0, self::BUFFER_SIZE);
    }

    public function delete(int $count)
    {
        if (!$this->seekable) {
            return;
        }
        if (preg_match('/(\e\[[0-9;]m)+$/', $this->written, $match)) {
            $count += strlen($match[0]);
            $append = $match[0];
        }
        $str = mb_substr($this->written, -$count);
        $this->truncate(strlen($str));
        !isset($append) || $this->write($append);
    }

    public function deleteLine()
    {
        if (!$this->seekable) {
            return;
        }

        if (false !== $pos = strrpos($this->written, PHP_EOL)) {
            $this->truncate(strlen($this->written) - $pos - 1);
        } else {
            $this->truncate(strlen($this->written));
        }
    }

    protected function truncate(int $count)
    {
        $stat = fstat($this->resource);
        ftruncate($this->resource, $stat['size'] - $count);
        fseek($this->resource, 0, SEEK_END);
        $this->written = substr($this->written, 0, -$count);
    }
}
