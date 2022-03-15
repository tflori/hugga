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

    protected function setUp(): void
    {
        $this->console = $this->createConsoleMock(false);
        $this->formatter = $this->consoleMocks['formatter'];
        $this->output = $this->consoleMocks['output'];
        $this->input = $this->consoleMocks['input'];
        $this->error = $this->consoleMocks['error'];
    }

    /**
     * Overwrite a protected or private $property from $object to $value
     *
     * @param object $object
     * @param string $property
     * @param mixed  $value
     */
    protected static function setProtectedProperty($object, string $property, $value)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $property = (new \ReflectionClass($object))->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }
}
