<?php

namespace Hugga\Test\Output\Drawing;

use Hugga\Output\Drawing\Table;
use Hugga\Test\TestCase;

class TableTest extends TestCase
{
    /** @test */
    public function tableWithoutHeadersFromIndexedArrays()
    {
        $table = new Table($this->console, $this->buildNamesData(['first', 'last', 'name'], 'indexed'));

        self::assertSame(
            '╭────────┬──────┬─────────────╮' . PHP_EOL .
            '│ foo    │ bar  │ Foo Bar     │' . PHP_EOL .
            '│ Arthur │ Dent │ Arthur Dent │' . PHP_EOL .
            '│ John   │ Doe  │ John Doe    │' . PHP_EOL .
            '╰────────┴──────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function tableWithoutHeadersFromAssocArrays()
    {
        $table = new Table($this->console, $this->buildNamesData(['last', 'first', 'name'], 'assoc'));

        self::assertSame(
            '╭──────┬────────┬─────────────╮' . PHP_EOL .
            '│ bar  │ foo    │ Foo Bar     │' . PHP_EOL .
            '│ Dent │ Arthur │ Arthur Dent │' . PHP_EOL .
            '│ Doe  │ John   │ John Doe    │' . PHP_EOL .
            '╰──────┴────────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function tableWithoutHeadersFromObjects()
    {
        $table = new Table($this->console, $this->buildNamesData(['last', 'first', 'name'], 'object'));

        self::assertSame(
            '╭──────┬────────┬─────────────╮' . PHP_EOL .
            '│ bar  │ foo    │ Foo Bar     │' . PHP_EOL .
            '│ Dent │ Arthur │ Arthur Dent │' . PHP_EOL .
            '│ Doe  │ John   │ John Doe    │' . PHP_EOL .
            '╰──────┴────────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function numericColumnsAreRightAlignedByDefault()
    {
        $table = new Table($this->console, $this->buildNamesData(['id', 'name'], 'indexed'));

        self::assertSame(
            '╭──────┬─────────────╮' . PHP_EOL .
            '│    1 │ Foo Bar     │' . PHP_EOL .
            '│   42 │ Arthur Dent │' . PHP_EOL .
            '│ 5432 │ John Doe    │' . PHP_EOL .
            '╰──────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function formattingDoesNotBreakTheLayout()
    {
        $table = new Table($this->console, $this->buildNamesData(['id', 'name', 'mail'], 'indexed'));

        self::assertSame(
            '╭──────┬─────────────┬──────────────╮' . PHP_EOL .
            '│    1 │ Foo Bar     │ ${red}invalid${r}      │' . PHP_EOL .
            '│   42 │ Arthur Dent │ adent@ex.com │' . PHP_EOL .
            '│ 5432 │ John Doe    │ jdoe@ex.com  │' . PHP_EOL .
            '╰──────┴─────────────┴──────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function tableWithHeadersByKeys()
    {
        $table = new Table($this->console, $this->buildNamesData(['last', 'first', 'name'], 'assoc'));

        $table->headersFromKeys();

        self::assertSame(
            '╭──────┬────────┬─────────────╮' . PHP_EOL .
            '│ ${b}last${r} │ ${b}first ${r} │ ${b}name       ${r} │' . PHP_EOL .
            '├──────┼────────┼─────────────┤' . PHP_EOL .
            '│ bar  │ foo    │ Foo Bar     │' . PHP_EOL .
            '│ Dent │ Arthur │ Arthur Dent │' . PHP_EOL .
            '│ Doe  │ John   │ John Doe    │' . PHP_EOL .
            '╰──────┴────────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function tableWithHeadersFromConstructor()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'first', 'name'], 'indexed'),
            ['Last Name', 'First Name', 'Full Name']
        );

        self::assertSame(
            '╭──────┬────────┬─────────────╮' . PHP_EOL .
            '│ ${b}Las…${r} │ ${b}First…${r} │ ${b}Full Name  ${r} │' . PHP_EOL .
            '├──────┼────────┼─────────────┤' . PHP_EOL .
            '│ bar  │ foo    │ Foo Bar     │' . PHP_EOL .
            '│ Dent │ Arthur │ Arthur Dent │' . PHP_EOL .
            '│ Doe  │ John   │ John Doe    │' . PHP_EOL .
            '╰──────┴────────┴─────────────╯',
            $table->getText()
        );
    }

    protected function buildNamesData($cols = ['first', 'last', 'name'], $type = 'indexed')
    {
        $baseData = [
            ['id' => 1, 'first' => 'foo', 'last' => 'bar', 'name' => 'Foo Bar', 'mail' => '${red}invalid'],
            ['id' => 42, 'first' => 'Arthur', 'last' => 'Dent', 'name' => 'Arthur Dent', 'mail' => 'adent@ex.com'],
            ['id' => 5432, 'first' => 'John', 'last' => 'Doe', 'name' => 'John Doe', 'mail' => 'jdoe@ex.com'],
        ];

        return array_map(function ($row) use ($type, $cols) {
            $r = $type === 'object' ? new \stdClass() : [];
            foreach ($cols as $key) {
                switch ($type) {
                    case 'indexed':
                        $r[] = $row[$key] ?? null;
                        break;
                    case 'assoc':
                        $r[$key] = $row[$key] ?? null;
                        break;
                    case 'object':
                        $r->$key = $row[$key] ?? null;
                        break;
                }
            }
            return $r;
        }, $baseData);
    }
}
