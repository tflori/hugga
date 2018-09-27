<?php

namespace Hugga\Test\Input\Question;

use Hugga\Input\Question\Choice;
use Hugga\Test\TestCase;

class ChoiceTest extends TestCase
{
    /*******************
     * Non-Interactive *
     *******************/

    /** @test */
    public function usesNonInteractiveMode()
    {
        $this->console->shouldReceive('isInteractive')->with()
            ->once()->andReturn(false);
        $this->console->shouldReceive('readLine')->with('> ')
            ->once()->andReturn(PHP_EOL);

        (new Choice(['a', 'b']))->ask($this->console);
    }
}
