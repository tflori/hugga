<?php

namespace Hugga\Input;

class ReadlineHandler extends AbstractInputHandler
{
    public static function isCompatible($resource)
    {
        return parent::isCompatible($resource) && stream_isatty($resource) && STDIN === $resource;
    }

    public function readLine(string $prompt = null): string
    {
        return $this->phpReadline('', $prompt ?? " \e[D");
    }

    public function read(int $count = 1, string $prompt = null): string
    {
        $str = $this->readConditional(function ($str) use ($count) {
            return strlen($str) >= $count;
        }, $prompt);
        return $str;
    }

    public function readUntil(string $sequence, string $prompt = null): string
    {
        $seqLen = strlen($sequence);
        $str = $this->readConditional(function ($str) use ($sequence, $seqLen) {
            return substr($str, -$seqLen) === $sequence;
        }, $prompt);
        return substr($str, 0, -$seqLen);
    }

    protected function readConditional(callable $conditionMet, string $prompt = null): string
    {
        $str = $previous = '';
        $this->phpReadline('callback_handler_install', $prompt ?? " \e[D", function ($str) use (&$previous) {
            $previous .= $str . PHP_EOL;
        });
        do {
            $r = array($this->resource);
            $n = stream_select($r, $w, $e, null);
            if ($n && in_array($this->resource, $r)) {
                $this->phpReadline('callback_read_char');
                $str = $previous . $this->phpReadline('info', 'line_buffer');
            }
        } while (!$conditionMet($str));
        $this->phpReadline('callback_handler_remove');

        return $str;
    }

    /**
     * Calls phps readline_* methods
     *
     * @param string $method
     * @param mixed ...$args
     * @return mixed
     * @codeCoverageIgnore we can not test this
     */
    protected function phpReadline(string $method, ...$args)
    {
        $method = 'readline' . (strlen($method) ? '_' . $method : '');
        return call_user_func_array($method, $args);
    }
}
