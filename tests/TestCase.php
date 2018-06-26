<?php

namespace Hugga\Test;

use Hugga\Console;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class TestCase extends MockeryTestCase
{
    /** @var Console|m\Mock */
    protected $console;

    /** @var resource */
    protected $stdout;
    /** @var resource */
    protected $stdin;
    /** @var resource */
    protected $stderr;

    protected function setUp()
    {
        /** @var Console|m\Mock $console */
        $console = $this->console = m::mock(Console::class)->makePartial();
        $stdout = $this->stdout = fopen('/tmp/hugga-test.stdout', 'w+');
        $console->setStdout($stdout);
        $stdin = $this->stdin = fopen('/tmp/hugga-test.stdin', 'w+');
        $console->setStdin($stdin);
        $stderr = $this->stderr = fopen('/tmp/hugga-test.stderr', 'w+');
        $console->setStderr($stderr);
    }
}
