<?php

namespace Hugga\Test;

use Hugga\Console;
use Hugga\Formatter;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class TestCase extends MockeryTestCase
{
    /** @var Console|m\Mock */
    protected $console;

    /** @var Formatter|m\Mock */
    protected $formatter;

    /** @var resource */
    protected $stdout;
    /** @var resource */
    protected $stdin;
    /** @var resource */
    protected $stderr;

    protected function setUp()
    {
        /** @var Formatter $formatter */
        $formatter = $this->formatter = m::mock(Formatter::class)->makePartial();
        /** @var Console|m\Mock $console */
        $console = $this->console = m::mock(Console::class)->makePartial();
        $console->__construct(null, $formatter);
        $stdout = $this->stdout = fopen('php://memory', 'w+');
        $console->setStdout($stdout);
        $stdin = $this->stdin = fopen('php://temp', 'w+');
        $console->setStdin($stdin);
        $stderr = $this->stderr = fopen('php://memory', 'w+');
        $console->setStderr($stderr);
    }
}
