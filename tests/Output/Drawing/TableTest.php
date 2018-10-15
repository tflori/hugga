<?php

namespace Hugga\Test\Output\Drawing;

use Hugga\Output\Drawing\Table;
use Hugga\Test\TestCase;

class TableTest extends TestCase
{
    /** @dataProvider provideSimpleArrays
     * @param $data
     * @param $expected
     * @test*/
    public function drawsASimpleTable(callable $settings, $data, $expected)
    {
        $table = new Table($this->console, $data);
        $settings($table);

        self::assertSame($expected, $table->getText());
    }

    public function provideSimpleArrays()
    {
        return [
            [function (Table $table) {
            }, [
                ['row1', 'hello world'],
                ['row2', 'foo bar'],
                ['last row', 'John Doe']
            ], '┌──────────┬─────────────┐' . PHP_EOL .
               '│ row1     │ hello world │' . PHP_EOL .
               '│ row2     │ foo bar     │' . PHP_EOL .
               '│ last row │ John Doe    │' . PHP_EOL .
               '└──────────┴─────────────┘'
            ],
            [function (Table $table) {
                $table->withoutBorder();
            }, [
                 ['row1', 'hello world'],
                 ['row2', 'foo bar'],
                 ['last row', 'John Doe']
             ], 'row1      hello world' . PHP_EOL .
                'row2      foo bar    ' . PHP_EOL .
                'last row  John Doe   '
            ],
            [function (Table $table) {
                $table->withBorderRows();
            }, [
                 ['row1', 'hello world'],
                 ['row2', 'foo bar'],
                 ['last row', 'John Doe']
             ], '┌──────────┬─────────────┐' . PHP_EOL .
                '│ row1     │ hello world │' . PHP_EOL .
                '├──────────┼─────────────┤' . PHP_EOL .
                '│ row2     │ foo bar     │' . PHP_EOL .
                '├──────────┼─────────────┤' . PHP_EOL .
                '│ last row │ John Doe    │' . PHP_EOL .
                '└──────────┴─────────────┘'
            ],
        ];
    }
}
