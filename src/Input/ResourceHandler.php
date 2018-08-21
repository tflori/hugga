<?php

namespace Hugga\Input;

class ResourceHandler extends AbstractInputHandler
{
    const BUFFER_SIZE = 4096;

    public function readLine()
    {
        // reset the position to read new lines
        fseek($this->resource, ftell($this->resource));
        return (string)fgets($this->resource);
    }

    public function read(int $count)
    {
        // reset the position to read new lines
        fseek($this->resource, ftell($this->resource));
        return fread($this->resource, $count);
    }

    public function readUntil(string $sequence)
    {
        $currentPos = ftell($this->resource);
        $seqLen = strlen($sequence);
        $buffer = '';
        do {
            $buffer .= fread($this->resource, self::BUFFER_SIZE);
            $pos = strpos($buffer, $sequence);
        } while (false === $pos && !feof($this->resource));

        if ($pos === false) {
            return $buffer;
        }

        $str = substr($buffer, 0, $pos);
        $strLen = strlen($str);
        if ((strlen($buffer) - $seqLen) > $strLen) {
            echo 'seeking to ' . ($currentPos + $strLen + $seqLen) . PHP_EOL;
            fseek($this->resource, $currentPos + $strLen + $seqLen);
        }

        return $str;
    }
}
