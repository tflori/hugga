<?php

namespace Hugga\Test;

use Hugga\Console;
use Hugga\Formatter;
use Hugga\Input\File;
use Hugga\MocksConsole;
use Hugga\Output\Tty;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class TestCase extends MockeryTestCase
{
    use MocksConsole;

    /** @var Console|m\Mock */
    protected $console;

    /** @var Formatter|m\Mock */
    protected $formatter;

    /** @var Tty|m\Mock */
    protected $output;

    /** @var File|m\Mock */
    protected $input;

    /** @var Tty|m\Mock */
    protected $error;

    protected function setUp()
    {
        $this->console = $this->createConsoleMock(false);
        $this->formatter = $this->consoleMocks['formatter'];
        $this->output = $this->consoleMocks['output'];
        $this->input = $this->consoleMocks['input'];
        $this->error = $this->consoleMocks['error'];
    }
}
