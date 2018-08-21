<?php

namespace Hugga\Input;

class TtyHandler extends AbstractInputHandler
{
    public function readLine()
    {
        return fgets($this->resource);
    }

    public function read(int $count)
    {
        for ($str = '', $i = 0; $i < $count; $i++) {
            $str .= $this->readMultibyte();
        }
        return $str;
    }

    public function readUntil(string $sequence)
    {
        $str = '';
        $seqLen = strlen($sequence);
        while (true) {
            $str .= $this->readMultibyte();
            if (substr($str, -$seqLen) === $sequence) {
                $str = substr($str, 0, -$seqLen);
                break;
            }
        }
        return $str;
    }

    public function readMultibyte($echo = true)
    {
        // change tty settings
        $sttySettings = preg_replace('#.*; ?#s', '', $this->ttySettings('--all'));
        $echo = $echo ? '' : '-echo';
        $this->ttySettings("cbreak $echo");

        $c = '';
        do {
            $c .= fgetc(STDIN);
            list($input, $output, $error) = [[STDIN], [], []];
        } while (stream_select($input, $output, $error, 0));

        // reset tty settings
        $this->ttySettings($sttySettings);

        return $c;
    }

    protected function ttySettings($options)
    {
        exec($cmd = "/bin/stty $options", $output, $returnValue);
        if ($returnValue) {
            throw new \Exception('Failed to change tty settings');
        }
        return implode(' ', $output);
    }
}
