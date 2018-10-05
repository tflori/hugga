<?php

namespace Hugga;

use Hugga\Input;
use Hugga\Output;
use Mockery as m;

trait MocksConsole
{
    /** @var m\Mock[] */
    protected $consoleMocks = [];

    /** @var resource */
    protected $stdout;
    /** @var resource */
    protected $stdin;
    /** @var resource */
    protected $stderr;

    /**
     * @param bool $interactive
     * @return m\MockInterface|Console
     */
    protected function createConsoleMock($interactive = false): m\MockInterface
    {
        $formatter = $this->consoleMocks['formatter'] = m::mock(Formatter::class)->makePartial();
        $console = $this->consoleMocks['console'] = m::mock(Console::class)->makePartial();
        $console->__construct(null, $formatter);


        $class = $interactive ? Output\Tty::class : Output\File::class;
        $this->stdout = fopen('php://memory', 'w+');
        $output = $this->consoleMocks['output'] = m::mock($class, [$console, $this->stdout])->makePartial();
        $console->setStdout($output);
        $this->stderr = fopen('php://memory', 'w+');
        $error = $this->consoleMocks['error'] = m::mock($class, [$console, $this->stderr])->makePartial();
        $console->setStderr($error);

        $class = $interactive ? Input\Readline::class : Input\File::class;
        $this->stdin = fopen('php://temp', 'w+');
        $input = $this->consoleMocks['input'] = m::mock($class, [$console, $this->stdin])->makePartial();
        $console->setStdin($input);

        if ($interactive) {
            $observer = $this->consoleMocks['observer'] = m::mock(Input\ObserverFaker::class)->makePartial();
            $console->shouldReceive('getInputObserver')->andReturn($observer)->byDefault();

            $output->shouldReceive('getSize')->andReturn([20, 80])->byDefault();
        }

        return $console;
    }
}
