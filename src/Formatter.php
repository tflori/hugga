<?php

namespace Hugga;

class Formatter
{
    protected static $formats = [
        'reset' => '0',
        'r' => '0', // shortcut for reset
        'bold' => '1',
        'b' => '1', // shortcut for bold
        'underline' => '4',
        'u' => '4', // shortcut for underline
        'blink' => '5',
        'invert' => '7',
        'hidden' => '8',
    ];

    protected static $fgColors = [
        'default' => '39',
        'black' => '30',
        'red' => '31',
        'green' => '32',
        'yellow' => '33',
        'blue' => '34',
        'magenta' => '35',
        'cyan' => '36',
        'grey' => '37',
        'dark-gray' => '90',
        'light-red' => '91',
        'light-green' => '92',
        'light-yellow' => '93',
        'light-blue' => '94',
        'light-magenta' => '95',
        'light-cyan' => '96',
        'white' => '97',
    ];

    protected static $bgColors = [
        'default' => '49',
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'grey' => '47',
        'dark-gray' => '100',
        'light-red' => '101',
        'light-green' => '102',
        'light-yellow' => '103',
        'light-blue' => '104',
        'light-magenta' => '105',
        'light-cyan' => '106',
        'white' => '107',
    ];

    protected $regexDefinition = '[A-Za-z0-9:; -]+';
    protected $regexTag = '\$\{\s*()\s*\}';

    /**
     * Format a message
     *
     * @param string $message
     * @return string
     */
    public function format(string $message)
    {
        $reset = $this->getEscapeSequence('reset');
        if (substr($message, -1) === "\n") {
            $reset .= "\n";
            $message = substr($message, 0, -1);
        }
        return $this->replaceFormatting($message) . $reset;
    }

    /**
     *
     * @param string $message
     * @return mixed|string
     */
    public function stripFormatting(string $message)
    {
        return $this->replaceFormatting($message, true);
    }

    protected function replaceFormatting(string $message, $strip = false)
    {
        $offset = 0;
        $regEx = '/' . str_replace('()', '(' . $this->regexDefinition . ')', $this->regexTag) . '/';
        $buffer = $message;

        while (preg_match($regEx, $buffer, $match, PREG_OFFSET_CAPTURE, $offset)) {
            $pos = $match[0][1];
            $len = strlen($match[0][0]);
            $replace = '';
            $format = true;

            // check for escaping characters in front
            if (preg_match('/(?:[^\\\\]|^)(\\\\+)$/', substr($buffer, 0, $pos), $backslashes)) {
                $count = strlen($backslashes[1]);
                $pos -= $count;
                $len += $count;
                // odd number means that we output the formatting definition instead
                if ($count % 2 == 1) {
                    $replace = str_repeat('\\', ($count - 1) / 2) . $match[0][0];
                    $format = false;
                } else {
                    // replace double backslash with single backslash
                    $replace = str_repeat('\\', $count / 2);
                }
            }

            // when we have to format we append it to the backslashes from escaping
            if ($format && !$strip) {
                $replace .= $this->getEscapeSequence($match[1][0]);
            }

            $buffer = substr_replace($buffer, $replace, $pos, $len);
            $offset = $pos + strlen($replace);
        }

        return $buffer;
    }

    /**
     * Get the escape sequence(s) for $def
     *
     * $def can be anything that is defined static::$formats, just a foreground color name defined in static::$fgColors,
     * or prefixed color name or number like `bg:cyan` or `fg:256`.
     *
     * In this function we don't test if the terminal supports a code. When the terminal does not support the code
     * it is simply not used. So keep in mind that many terminals don't support dim, blink and hidden.
     *
     * @param string $def
     * @return string
     */
    protected function getEscapeSequence(string $def): string
    {
        if (strpos($def, ';') !== false) {
            return implode('', array_map([$this, 'getEscapeSequence'], explode(';', $def)));
        }

        if ('' === $def = trim($def)) {
            return '';
        }

        if (isset(static::$formats[$def])) {
            return $this->escape(static::$formats[$def]);
        } elseif (substr($def, 0, 3) === 'fg:') {
            if (is_numeric($color = substr($def, 3)) && $color <= 256) {
                return $this->escape("38;5;" . $color);
            } elseif (isset(static::$fgColors[$color])) {
                return $this->escape(static::$fgColors[$color]);
            }
        } elseif (substr($def, 0, 3) === 'bg:') {
            if (is_numeric($color = substr($def, 3)) && $color <= 256) {
                return $this->escape("48;5;" . $color);
            } elseif (isset(static::$bgColors[$color])) {
                return $this->escape(static::$bgColors[$color]);
            }
        } elseif (isset(static::$fgColors[$def])) {
            return $this->escape(static::$fgColors[$def]);
        }

        return '';
    }

    protected function escape(string $code): string
    {
        return sprintf("\e[%sm", $code);
    }
}
