<?php

namespace Hugga\Test\Examples;

use Hugga\DrawingInterface;

class Drawing implements DrawingInterface
{
    public function getText(): string
    {
        // a example table
        return
            '| ${b}id${r} | ${b}name${r}  |' . PHP_EOL .
            '|----|-------|' . PHP_EOL .
            '|  1 | Alice |' . PHP_EOL .
            '|  2 | Bob   |';
    }
}
