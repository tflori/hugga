<?php

namespace Hugga\Output\Drawing;

use Hugga\Console;
use Hugga\DrawingInterface;

class Table implements DrawingInterface
{
    const CORNER_TOP_LEFT = 'ctl';
    const CORNER_TOP_RIGHT = 'ctr';
    const CORNER_BOTTOM_LEFT = 'cbl';
    const CORNER_BOTTOM_RIGHT = 'cbr';
    const BORDER_HORIZONTAL = 'bho';
    const BORDER_VERTICAL = 'bve';
    const TEE_HORIZONTAL_DOWN = 'thd';
    const TEE_HORIZONTAL_UP = 'thu';
    const TEE_VERTICAL_RIGHT = 'tvr';
    const TEE_VERTICAL_LEFT = 'tvl';
    const CROSS = 'cro';

    protected $console;

    protected $data;
    protected $headers;
    protected $columns;

    protected $withBorder = true;
    protected $borderBetweenRows = false;
    protected $padding = 1;
    protected $repeatHeader = 0;
    protected $headerStyle = '${b}';

    protected $borderStyle = [
        self::CORNER_TOP_LEFT  => '┌',
        self::CORNER_TOP_RIGHT => '┐',
        self::CORNER_BOTTOM_LEFT => '└',
        self::CORNER_BOTTOM_RIGHT => '┘',
        self::BORDER_HORIZONTAL => '─',
        self::BORDER_VERTICAL => '│',
        self::TEE_HORIZONTAL_DOWN => '┬',
        self::TEE_HORIZONTAL_UP => '┴',
        self::TEE_VERTICAL_LEFT => '┤',
        self::TEE_VERTICAL_RIGHT => '├',
        self::CROSS => '┼'
    ];

    /**
     * Table constructor.
     *
     * @param Console $console
     * @param array $data
     * @param array|null $headers
     */
    public function __construct(Console $console, array $data, array $headers = null)
    {
        $this->console = $console;
        $this->data = $data;
        $this->headers = $headers;
    }

    public function withoutBorder()
    {
        $this->withBorder = false;
        return $this;
    }

    public function withBorderRows()
    {
        $this->borderBetweenRows = true;
        return $this;
    }

    public function withoutBorderRows()
    {
        $this->borderBetweenRows = false;
        return $this;
    }

    public function padding(int $padding)
    {
        $this->padding = $padding;
        return $this;
    }

    public function repeatHeaders(int $every = 10)
    {
        $this->repeatHeader = $every;
        return $this;
    }

    public function headerStyle(string $format)
    {
        $this->headerStyle = $format;
        return $this;
    }

    public function headersFromKeys($row = 0)
    {
        $this->headers = array_combine(array_keys($this->data[$row]), array_keys($this->data[$row]));
        return $this;
    }

    public function column($key, $definition)
    {
        $columns = $this->getColumnDefinitions();
        if (!isset($columns[$key])) {
            throw new \LogicException('There is no column $key in our data');
        }

        foreach ($definition as $var => $value) {
            switch ($var) {
                case 'header':
                    $this->headers[$key] = $value;
                    break;
                case 'delete':
                    unset($this->columns[$key]);
                    break;
                default:
                    $this->columns[$key]->$var = $value;
            }
        }

        return $this;
    }

    public function getColumnDefinitions(): array
    {
        if (!$this->columns) {
            $columns = [];
            foreach ($this->data as $row) {
                foreach ($row as $key => $value) {
                    $width = $this->console->strLen($value);

                    if (!isset($columns[$key])) {
                        $columns[$key] = (object)[
                            'type' => is_numeric($value) ? 'number' : 'string',
                            'width' => $width,
                        ];
                        $columns[$key]->align = $columns[$key]->type === 'number' ? 'right' : 'left';
                        continue;
                    }

                    if ($columns[$key]->type === 'number' && !is_numeric($value)) {
                        $columns[$key]->type = 'string';
                        $columns[$key]->align = 'left';
                    }

                    if ($width > $columns[$key]->width) {
                        $columns[$key]->width = $width;
                    }
                }
            }
            $this->columns = $columns;
        }

        return $this->columns;
    }

    /**
     * Get the output for this drawing.
     *
     * The drawing may include formatting and line breaks.
     * It should never change the amount of rows.
     *
     * @return string
     */
    public function getText(): string
    {
        $columns = $this->getColumnDefinitions();
        $rows = [];

        array_push($rows, ...$this->getRows($columns, $this->data));
        $borderRow = $this->getBorderRow($columns);

        if ($this->borderBetweenRows) {
            $this->repeatRow($rows, $borderRow);
        }

        if ($this->headers) {
            $headerRow = $this->getHeaderRow($columns);
            if ($this->repeatHeader) {
                if ($this->borderBetweenRows) {
                    $this->repeatRow($rows, [$headerRow, $borderRow], $this->repeatHeader * 2);
                } else {
                    $this->repeatRow($rows, [$borderRow, $headerRow, $borderRow], $this->repeatHeader);
                }
            }
            array_unshift($rows, $headerRow, $borderRow);
        }

        if ($this->withBorder) {
            array_unshift($rows, $this->getTopBorderRow($columns));
            array_push($rows, $this->getBottomBorderRow($columns));
        }

        return implode(PHP_EOL, $rows);
    }

    protected function getRows(array $columns, array $data): array
    {
        $rows = [];
        list($left, $right, $spacer) = $this->getDivider();

        foreach ($data as $row) {
            $r = [];
            foreach ($columns as $key => $column) {
                $value = is_object($row) ? $row->$key ?? null : $row[$key] ?? null;
                if (!$value) {
                    $r[] = str_repeat(' ', $column->width);
                    continue;
                }

                switch ($column->type) {
                    case 'number':
                        $value = isset($column->format) ? sprintf($column->format, $value) : (string)$value;
                        break;

                    case 'string':
                        $value = (string)$value;
                        break;
                }

                $width = $this->console->strLen($value);
                switch ($column->align) {
                    case 'left':
                        $r[] = $value . str_repeat(' ', $column->width - $width);
                        break;

                    case 'right':
                        $r[] = str_repeat(' ', $column->width - $width) . $value;
                        break;

                    case 'center':
                        $r[] = str_repeat(' ', ceil(($column->width - $width) / 2)) . $value .
                               str_repeat(' ', floor(($column->width - $width) / 2));
                        break;
                }
            }
            $rows[] = $left . implode($spacer, $r) . $right;
        }

        return $rows;
    }

    protected function getHeaderRow(array $columns)
    {
        list($left, $right, $spacer) = $this->getDivider();

        $r = [];
        foreach ($columns as $key => $column) {
            if (!isset($this->headers[$key])) {
                $r[] = str_repeat(' ', $column->width);
                continue;
            }

            $value = $this->headers[$key];
            $width = $this->console->strLen($value);
            if ($width > $column->width) {
                $value = substr($this->headers[$key], 0, $column->width - $width - 1) . '…';
                $width = $this->console->strLen($value);
            }
            $r[] = $this->headerStyle . $value . str_repeat(' ', $column->width - $width) . '${r}';
        }

        return $left . implode($spacer, $r) . $right;
    }

    protected function getTopBorderRow(array $columns)
    {
        $r = [];
        foreach ($columns as $column) {
            $r[] = str_repeat($this->borderStyle[self::BORDER_HORIZONTAL], $column->width + $this->padding * 2);
        }

        return $this->borderStyle[self::CORNER_TOP_LEFT] .
               implode($this->borderStyle[self::TEE_HORIZONTAL_DOWN], $r) .
               $this->borderStyle[self::CORNER_TOP_RIGHT];
    }

    protected function getBottomBorderRow(array $columns)
    {
        $r = [];
        foreach ($columns as $column) {
            $r[] = str_repeat($this->borderStyle[self::BORDER_HORIZONTAL], $column->width + $this->padding * 2);
        }

        return $this->borderStyle[self::CORNER_BOTTOM_LEFT] .
               implode($this->borderStyle[self::TEE_HORIZONTAL_UP], $r) .
               $this->borderStyle[self::CORNER_BOTTOM_RIGHT];
    }

    protected function getBorderRow(array $columns)
    {
        list($left, $right, $spacer) = $this->getDivider(
            $this->borderStyle[self::BORDER_HORIZONTAL],
            $this->borderStyle[self::TEE_VERTICAL_RIGHT],
            $this->borderStyle[self::TEE_VERTICAL_LEFT],
            $this->borderStyle[self::CROSS]
        );

        $r = [];
        foreach ($columns as $column) {
            $r[] = str_repeat($this->borderStyle[self::BORDER_HORIZONTAL], $column->width);
        }

        return $left . implode($spacer, $r) . $right;
    }

    protected function getDivider($padding = ' ', $borderLeft = null, $borderRight = null, $borderInside = null)
    {
        $borderLeft   = $borderLeft   ?? $this->borderStyle[self::BORDER_VERTICAL];
        $borderRight  = $borderRight  ?? $this->borderStyle[self::BORDER_VERTICAL];
        $borderInside = $borderInside ?? $this->borderStyle[self::BORDER_VERTICAL];

        $left = '';
        $right = '';
        $spacer = str_repeat($padding, $this->padding * 2);
        if ($this->withBorder) {
            $left = $borderLeft . str_repeat($padding, $this->padding);
            $right = str_repeat($padding, $this->padding) . $borderRight;
            $spacer = str_repeat($padding, $this->padding) . $borderInside . str_repeat($padding, $this->padding);
        }

        return [$left, $right, $spacer];
    }

    protected function repeatRow(array &$rows, $repeat, int $every = 1)
    {
        $count = ceil(count($rows) / $every) - 1;
        for ($i = 0; $i < $count; $i++) {
            array_splice($rows, $i * ($every + (is_array($repeat) ? count($repeat) : 1)) + $every, 0, $repeat);
        }
    }
}
