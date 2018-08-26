<?php

namespace Hugga\Input;

class FileHandler extends AbstractInputHandler
{
    const BUFFER_SIZE = 4096;

    public function readLine(string $prompt = null): string
    {
        // reset the position to read new lines
        fseek($this->resource, ftell($this->resource));
        return rtrim(fgets($this->resource));
    }

    public function read(int $count = 1, string $prompt = null): string
    {
        $currentPos = ftell($this->resource);
        // reset the position to read new lines
        fseek($this->resource, $currentPos);

        $buffer = fread($this->resource, 4 * $count);
        $str = mb_substr($buffer, 0, $count);

        fseek($this->resource, $currentPos + strlen($str));

        return $str;
    }

    public function readUntil(string $sequence, string $prompt = null): string
    {
        $currentPos = ftell($this->resource);
        // reset the position to read new lines
        fseek($this->resource, $currentPos);

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
            fseek($this->resource, $currentPos + $strLen + $seqLen);
        }

        return $str;
    }
}
