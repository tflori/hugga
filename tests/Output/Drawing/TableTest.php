<?php

namespace Hugga\Test\Output\Drawing;

use Hugga\Output\Drawing\Table;
use Hugga\Test\Examples\Row;
use Hugga\Test\Examples\Value;
use Hugga\Test\TestCase;

class TableTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        // reset the default style
        Table::setDefaultStyle([
            'border' => true,
            'bordersInside' => false,
            'padding' => 1,
            'repeatHeader' => 0,
            'headerStyle' => '${b}',
            'borderStyle' => [
                Table::CORNER_TOP_LEFT => '╭',
                Table::CORNER_TOP_RIGHT => '╮',
                Table::CORNER_BOTTOM_LEFT => '╰',
                Table::CORNER_BOTTOM_RIGHT => '╯',
                Table::BORDER_HORIZONTAL => '─',
                Table::BORDER_VERTICAL => '│',
                Table::TEE_HORIZONTAL_DOWN => '┬',
                Table::TEE_HORIZONTAL_UP => '┴',
                Table::TEE_VERTICAL_LEFT => '┤',
                Table::TEE_VERTICAL_RIGHT => '├',
                Table::CROSS => '┼'
            ],
        ]);
    }

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

    /** @test */
    public function tableWithHeadersFromConstructorForAssoc()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'first', 'name'], 'assoc'),
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

    /** @test */
    public function tableWithoutBorder()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'first', 'name'], 'indexed')
        );

        $table->borders(false);

        self::assertSame(
            'bar   foo     Foo Bar    ' . PHP_EOL .
            'Dent  Arthur  Arthur Dent' . PHP_EOL .
            'Doe   John    John Doe   ',
            $table->getText()
        );
    }

    /** @test */
    public function tableWithoutBordersButHeaders()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'first', 'name'], 'indexed')
        );

        $table->borders(false)->setHeaders(['Last Name', 'First Name', 'Full Name']);

        self::assertSame(
            '${b}Las…${r}  ${b}First…${r}  ${b}Full Name  ${r}' . PHP_EOL .
            'bar   foo     Foo Bar    ' . PHP_EOL .
            'Dent  Arthur  Arthur Dent' . PHP_EOL .
            'Doe   John    John Doe   ',
            $table->getText()
        );
    }

    /** @test */
    public function increaseColumnWidth()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'first', 'name'], 'assoc'),
            ['Last Name', 'First Name', 'Full Name']
        );

        $table->column('last', ['width' => 9]);

        self::assertSame(
            '╭───────────┬────────┬─────────────╮' . PHP_EOL .
            '│ ${b}Last Name${r} │ ${b}First…${r} │ ${b}Full Name  ${r} │' . PHP_EOL .
            '├───────────┼────────┼─────────────┤' . PHP_EOL .
            '│ bar       │ foo    │ Foo Bar     │' . PHP_EOL .
            '│ Dent      │ Arthur │ Arthur Dent │' . PHP_EOL .
            '│ Doe       │ John   │ John Doe    │' . PHP_EOL .
            '╰───────────┴────────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function increaseColumnWidthForHeaders()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'first', 'name'], 'indexed')
        );

        $table->setHeaders(['Last Name', 'First Name', 'Full Name'], true);

        self::assertSame(
            '╭───────────┬────────────┬─────────────╮' . PHP_EOL .
            '│ ${b}Last Name${r} │ ${b}First Name${r} │ ${b}Full Name  ${r} │' . PHP_EOL .
            '├───────────┼────────────┼─────────────┤' . PHP_EOL .
            '│ bar       │ foo        │ Foo Bar     │' . PHP_EOL .
            '│ Dent      │ Arthur     │ Arthur Dent │' . PHP_EOL .
            '│ Doe       │ John       │ John Doe    │' . PHP_EOL .
            '╰───────────┴────────────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function tableWithBordersInside()
    {
        $table = new Table($this->console, $this->buildNamesData(['first', 'last', 'name'], 'indexed'));

        $table->bordersInside(true);

        self::assertSame(
            '╭────────┬──────┬─────────────╮' . PHP_EOL .
            '│ foo    │ bar  │ Foo Bar     │' . PHP_EOL .
            '├────────┼──────┼─────────────┤' . PHP_EOL .
            '│ Arthur │ Dent │ Arthur Dent │' . PHP_EOL .
            '├────────┼──────┼─────────────┤' . PHP_EOL .
            '│ John   │ Doe  │ John Doe    │' . PHP_EOL .
            '╰────────┴──────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function tableWithRepeatedHeaders()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'first', 'name'], 'indexed')
        );
        $table->setHeaders(['Last Name', 'First Name', 'Full Name'], true);

        $table->repeatHeaders(2);

        self::assertSame(
            '╭───────────┬────────────┬─────────────╮' . PHP_EOL .
            '│ ${b}Last Name${r} │ ${b}First Name${r} │ ${b}Full Name  ${r} │' . PHP_EOL .
            '├───────────┼────────────┼─────────────┤' . PHP_EOL .
            '│ bar       │ foo        │ Foo Bar     │' . PHP_EOL .
            '│ Dent      │ Arthur     │ Arthur Dent │' . PHP_EOL .
            '├───────────┼────────────┼─────────────┤' . PHP_EOL .
            '│ ${b}Last Name${r} │ ${b}First Name${r} │ ${b}Full Name  ${r} │' . PHP_EOL .
            '├───────────┼────────────┼─────────────┤' . PHP_EOL .
            '│ Doe       │ John       │ John Doe    │' . PHP_EOL .
            '╰───────────┴────────────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function repeatedHeadersDoNotInterfereWithInsideBorders()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'first', 'name'], 'indexed')
        );
        $table->setHeaders(['Last Name', 'First Name', 'Full Name'], true);

        $table->repeatHeaders(2)->bordersInside(true);

        self::assertSame(
            '╭───────────┬────────────┬─────────────╮' . PHP_EOL .
            '│ ${b}Last Name${r} │ ${b}First Name${r} │ ${b}Full Name  ${r} │' . PHP_EOL .
            '├───────────┼────────────┼─────────────┤' . PHP_EOL .
            '│ bar       │ foo        │ Foo Bar     │' . PHP_EOL .
            '├───────────┼────────────┼─────────────┤' . PHP_EOL .
            '│ Dent      │ Arthur     │ Arthur Dent │' . PHP_EOL .
            '├───────────┼────────────┼─────────────┤' . PHP_EOL .
            '│ ${b}Last Name${r} │ ${b}First Name${r} │ ${b}Full Name  ${r} │' . PHP_EOL .
            '├───────────┼────────────┼─────────────┤' . PHP_EOL .
            '│ Doe       │ John       │ John Doe    │' . PHP_EOL .
            '╰───────────┴────────────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function doesNotRepeatAtEnd()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'first', 'name'], 'indexed')
        );
        $table->setHeaders(['Last Name', 'First Name', 'Full Name'], true);

        $table->repeatHeaders(3);

        self::assertSame(
            '╭───────────┬────────────┬─────────────╮' . PHP_EOL .
            '│ ${b}Last Name${r} │ ${b}First Name${r} │ ${b}Full Name  ${r} │' . PHP_EOL .
            '├───────────┼────────────┼─────────────┤' . PHP_EOL .
            '│ bar       │ foo        │ Foo Bar     │' . PHP_EOL .
            '│ Dent      │ Arthur     │ Arthur Dent │' . PHP_EOL .
            '│ Doe       │ John       │ John Doe    │' . PHP_EOL .
            '╰───────────┴────────────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function mixedDataIsShownLeftAligned()
    {
        $table = new Table(
            $this->console,
            [
                [42, 'Arthur Dent'],
                ['null', 'Foo Bar'],
            ]
        );

        self::assertSame(
            '╭──────┬─────────────╮' . PHP_EOL .
            '│ 42   │ Arthur Dent │' . PHP_EOL .
            '│ null │ Foo Bar     │' . PHP_EOL .
            '╰──────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function nullValuesNotChangingColumnType()
    {
        $table = new Table(
            $this->console,
            [
                [42, 'Arthur Dent'],
                [null, 'Foo Bar'],
            ]
        );

        self::assertSame(
            '╭──────┬─────────────╮' . PHP_EOL .
            '│   42 │ Arthur Dent │' . PHP_EOL .
            '│ null │ Foo Bar     │' . PHP_EOL .
            '╰──────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function onlyNullValuesShowNullTextInArrays()
    {
        $table = new Table(
            $this->console,
            [
                ['id' => 42, 'name' => 'Arthur Dent'],
                ['id' => null],
            ]
        );

        self::assertSame(
            '╭──────┬─────────────╮' . PHP_EOL .
            '│   42 │ Arthur Dent │' . PHP_EOL .
            '│ null │             │' . PHP_EOL .
            '╰──────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function onlyNullValuesShowNullTextInObjects()
    {
        $table = new Table(
            $this->console,
            [
                (object)['id' => 42, 'name' => 'Arthur Dent'],
                (object)['id' => null],
            ]
        );

        self::assertSame(
            '╭──────┬─────────────╮' . PHP_EOL .
            '│   42 │ Arthur Dent │' . PHP_EOL .
            '│ null │             │' . PHP_EOL .
            '╰──────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function booleansShowAsTAndF()
    {
        $table = new Table(
            $this->console,
            [
                ['id' => 42, 'name' => 'Arthur Dent', 'activated' => true],
                ['id' => false, 'activated' => false],
            ]
        );

        self::assertSame(
            '╭────┬─────────────┬───╮' . PHP_EOL .
            '│ 42 │ Arthur Dent │ t │' . PHP_EOL .
            '│  f │             │ f │' . PHP_EOL .
            '╰────┴─────────────┴───╯',
            $table->getText()
        );
    }

    /** @test */
    public function objectsRequireToStringMethod()
    {
        $table = new Table(
            $this->console,
            [
                new Row(new Value(42), new Value('Arthur Dent'), new Value(true)),
                new Row(new Value(null), new Value(''), new Value(false)),
            ]
        );

        self::assertSame(
            '╭────┬─────────────┬───╮' . PHP_EOL .
            '│ 42 │ Arthur Dent │ 1 │' . PHP_EOL .
            '│    │             │   │' . PHP_EOL . // no conversions here!
            '╰────┴─────────────┴───╯',
            $table->getText()
        );
    }

    /** @test */
    public function formattingNumberColumns()
    {
        $table = new Table($this->console, $this->buildNamesData(['id', 'name'], 'assoc'));

        $table->column('id', ['format' => '%05d', 'width' => 5]);

        self::assertSame(
            '╭───────┬─────────────╮' . PHP_EOL .
            '│ 00001 │ Foo Bar     │' . PHP_EOL .
            '│ 00042 │ Arthur Dent │' . PHP_EOL .
            '│ 05432 │ John Doe    │' . PHP_EOL .
            '╰───────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function alignCenter()
    {
        $table = new Table($this->console, $this->buildNamesData(['id', 'name'], 'assoc'));

        $table->column('name', ['align' => 'center']);

        self::assertSame(
            '╭──────┬─────────────╮' . PHP_EOL .
            '│    1 │   Foo Bar   │' . PHP_EOL .
            '│   42 │ Arthur Dent │' . PHP_EOL .
            '│ 5432 │   John Doe  │' . PHP_EOL .
            '╰──────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function formattingHeaders()
    {
        $table = new Table($this->console, $this->buildNamesData(['id', 'name'], 'assoc'));
        $table->headersFromKeys();

        $table->headerStyle('{$b;red}');

        self::assertSame(
            '╭──────┬─────────────╮' . PHP_EOL .
            '│ {$b;red}id  ${r} │ {$b;red}name       ${r} │' . PHP_EOL .
            '├──────┼─────────────┤' . PHP_EOL .
            '│    1 │ Foo Bar     │' . PHP_EOL .
            '│   42 │ Arthur Dent │' . PHP_EOL .
            '│ 5432 │ John Doe    │' . PHP_EOL .
            '╰──────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function deleteColumns()
    {
        $table = new Table($this->console, $this->buildNamesData(['first', 'name'], 'assoc'));

        $table->column('first', ['delete' => true]);

        self::assertSame(
            '╭─────────────╮' . PHP_EOL .
            '│ Foo Bar     │' . PHP_EOL .
            '│ Arthur Dent │' . PHP_EOL .
            '│ John Doe    │' . PHP_EOL .
            '╰─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function changeBorderStyle()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'name'], 'assoc'),
            ['Last Name', 'Full Name']
        );

        $table->borderStyle([
            Table::BORDER_HORIZONTAL => '-',
            Table::BORDER_VERTICAL => '|',
            Table::CROSS => '+',
            Table::CORNER_BOTTOM_LEFT => '\\',
            Table::CORNER_BOTTOM_RIGHT => '/',
            // tees and corners are taken from cross if not specified
        ]);

        self::assertSame(
            '+------+-------------+' . PHP_EOL .
            '| ${b}Las…${r} | ${b}Full Name  ${r} |' . PHP_EOL .
            '+------+-------------+' . PHP_EOL .
            '| bar  | Foo Bar     |' . PHP_EOL .
            '| Dent | Arthur Dent |' . PHP_EOL .
            '| Doe  | John Doe    |' . PHP_EOL .
            '\------+-------------/',
            $table->getText()
        );
    }

    /** @test */
    public function changePadding()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'name'], 'assoc'),
            ['Last Name', 'Full Name']
        );

        $table->padding(3);

        self::assertSame(
            '╭──────────┬─────────────────╮' . PHP_EOL .
            '│   ${b}Las…${r}   │   ${b}Full Name  ${r}   │' . PHP_EOL .
            '├──────────┼─────────────────┤' . PHP_EOL .
            '│   bar    │   Foo Bar       │' . PHP_EOL .
            '│   Dent   │   Arthur Dent   │' . PHP_EOL .
            '│   Doe    │   John Doe      │' . PHP_EOL .
            '╰──────────┴─────────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function columnsWithoutHeaderAreFilledWithSpaces()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'name'], 'assoc')
        );

        $table->column('name', ['header' => 'Full Name']);

        self::assertSame(
            '╭──────┬─────────────╮' . PHP_EOL .
            '│      │ ${b}Full Name  ${r} │' . PHP_EOL .
            '├──────┼─────────────┤' . PHP_EOL .
            '│ bar  │ Foo Bar     │' . PHP_EOL .
            '│ Dent │ Arthur Dent │' . PHP_EOL .
            '│ Doe  │ John Doe    │' . PHP_EOL .
            '╰──────┴─────────────╯',
            $table->getText()
        );
    }

    /** @test */
    public function changingNonExistingColumnsThrows()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'name'], 'assoc')
        );

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('There is no column anything in our data');

        $table->column('anything', []);
    }

    /** @test */
    public function defaultStyleCanBeChanged()
    {
        Table::setDefaultStyle([
            'border' => false,
            'bordersInside' => true,
            'padding' => 2,
            'repeatHeader' => 2,
            'headerStyle' => '${red}',
            'borderStyle' => [
                Table::BORDER_HORIZONTAL => '-',
            ],
        ]);

        $table = new Table($this->console, $this->buildNamesData(), ['First', 'Last', 'Name']);

        self::assertSame(
            '${red}First ${r}    ${red}Last${r}    ${red}Name       ${r}' . PHP_EOL .
            'foo       bar     Foo Bar    ' . PHP_EOL .
            '-----------------------------' . PHP_EOL .
            'Arthur    Dent    Arthur Dent' . PHP_EOL .
            '-----------------------------' . PHP_EOL .
            '${red}First ${r}    ${red}Last${r}    ${red}Name       ${r}' . PHP_EOL .
            '-----------------------------' . PHP_EOL .
            'John      Doe     John Doe   ',
            $table->getText()
        );
    }

    /** @test */
    public function defaultStyleCanNotContainAdditionalDefinitions()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Default style can not contain columns');

        Table::setDefaultStyle(['columns' => ['foo' => ['width' => 30]]]);
    }

    /** @test */
    public function defaultStyleHasToMatchTypes()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('border has to be from type boolean');

        Table::setDefaultStyle(['border' => 0]);
    }

    /** @test */
    public function drawWritesTheTable()
    {
        $table = new Table(
            $this->console,
            $this->buildNamesData(['last', 'name'], 'assoc'),
            ['Last Name', 'Full Name']
        );

        $this->console->shouldReceive('line')->with(
            '╭──────┬─────────────╮' . PHP_EOL .
            '│ ${b}Las…${r} │ ${b}Full Name  ${r} │' . PHP_EOL .
            '├──────┼─────────────┤' . PHP_EOL .
            '│ bar  │ Foo Bar     │' . PHP_EOL .
            '│ Dent │ Arthur Dent │' . PHP_EOL .
            '│ Doe  │ John Doe    │' . PHP_EOL .
            '╰──────┴─────────────╯'
        )->once();

        $table->draw();
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
