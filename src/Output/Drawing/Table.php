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

    protected static $defaultStyle = [
        'border' => true,
        'bordersInside' => false,
        'padding' => 1,
        'repeatHeader' => 0,
        'headerStyle' => '${b}',
        'borderStyle' => [
            self::CORNER_TOP_LEFT => '╭',
            self::CORNER_TOP_RIGHT => '╮',
            self::CORNER_BOTTOM_LEFT => '╰',
            self::CORNER_BOTTOM_RIGHT => '╯',
            self::BORDER_HORIZONTAL => '─',
            self::BORDER_VERTICAL => '│',
            self::TEE_HORIZONTAL_DOWN => '┬',
            self::TEE_HORIZONTAL_UP => '┴',
            self::TEE_VERTICAL_LEFT => '┤',
            self::TEE_VERTICAL_RIGHT => '├',
            self::CROSS => '┼'
        ],
    ];

    protected $console;

    /** @var iterable */
    protected $data;
    protected $columns;

    protected $border = true;
    protected $bordersInside = false;
    protected $padding = 1;
    protected $repeatHeader = 0;
    protected $headerStyle = '${b}';

    protected $borderStyle = [
        self::CORNER_TOP_LEFT => '╭',
        self::CORNER_TOP_RIGHT => '╮',
        self::CORNER_BOTTOM_LEFT => '╰',
        self::CORNER_BOTTOM_RIGHT => '╯',
        self::BORDER_HORIZONTAL => '─',
        self::BORDER_VERTICAL => '│',
        self::TEE_HORIZONTAL_DOWN => '┬',
        self::TEE_HORIZONTAL_UP => '┴',
        self::TEE_VERTICAL_LEFT => '┤',
        self::TEE_VERTICAL_RIGHT => '├',
        self::CROSS => '┼'
    ];

    /**
     * Change the default style for tables
     *
     * The default default style is:
     * ```
     * [
     *      'border' => true,
     *      'bordersInside' => false,
     *      'padding' => 1,
     *      'repeatHeader' => 0,
     *      'headerStyle' => '${b}',
     *      'borderStyle' => [
     *          self::CORNER_TOP_LEFT => '╭',
     *          self::CORNER_TOP_RIGHT => '╮',
     *          self::CORNER_BOTTOM_LEFT => '╰',
     *          self::CORNER_BOTTOM_RIGHT => '╯',
     *          self::BORDER_HORIZONTAL => '─',
     *          self::BORDER_VERTICAL => '│',
     *          self::TEE_HORIZONTAL_DOWN => '┬',
     *          self::TEE_HORIZONTAL_UP => '┴',
     *          self::TEE_VERTICAL_LEFT => '┤',
     *          self::TEE_VERTICAL_RIGHT => '├',
     *          self::CROSS => '┼'
     *      ],
     * ]
     * ```
     * @param array $defaultStyle
     */
    public static function setDefaultStyle(array $defaultStyle)
    {
        foreach ($defaultStyle as $key => $value) {
            if (!array_key_exists($key, static::$defaultStyle)) {
                throw new \InvalidArgumentException('Default style can not contain ' . $key);
            }

            $type = gettype(static::$defaultStyle[$key]);
            if ($type !== gettype($value)) {
                throw new \InvalidArgumentException($key . ' has to be from type ' . $type);
            }

            switch ($type) {
                case 'array':
                    static::$defaultStyle[$key] = array_merge(static::$defaultStyle[$key], $value);
                    break;
                default:
                    static::$defaultStyle[$key] = $value;
            }
        }
    }

    /**
     * Table constructor.
     *
     * @param Console $console
     * @param iterable $data
     * @param array|null $headers
     */
    public function __construct(Console $console, iterable $data, array $headers = null)
    {
        $this->console = $console;
        $this->data = $data;
        !$headers || $this->setHeaders($headers);
        $this->applyDefaultStyle();
    }

    /**
     * Set / Change the headers
     *
     * Unless you pass $adjustWidth=true the width of the columns stays the same and longer column names are shortened
     *
     * @param array $headers
     * @param bool $adjustWidth
     * @return $this
     */
    public function setHeaders(array $headers, bool $adjustWidth = false)
    {
        $this->prepareColumns();

        if (array_keys($headers) === range(0, count($headers) - 1) &&
            array_keys($this->columns) !== range(0, count($this->columns) - 1)
        ) {
            $headers = array_combine(array_keys($this->columns), $headers);
        }

        foreach ($headers as $key => $header) {
            if ($adjustWidth && $this->columns[$key]->width < $width = $this->console->strLen($header)) {
                $this->columns[$key]->width = $width;
            }
            $this->columns[$key]->header = $header;
        }

        return $this;
    }

    /**
     * Enable / disable borders
     *
     * @param bool $borders
     * @return $this
     */
    public function borders(bool $borders = true)
    {
        $this->border = $borders;
        return $this;
    }

    /**
     * Enable / disable borders inside
     *
     * @param bool $bordersInside
     * @return $this
     */
    public function bordersInside(bool $bordersInside = false)
    {
        $this->bordersInside = $bordersInside;
        return $this;
    }

    /**
     * Set the padding in number of spaces
     *
     * @param int $padding
     * @return $this
     */
    public function padding(int $padding)
    {
        $this->padding = $padding;
        return $this;
    }

    /**
     * Repeat the headers every $n rows
     *
     * @param int $n
     * @return $this
     */
    public function repeatHeaders(int $n = 10)
    {
        $this->repeatHeader = $n;
        return $this;
    }

    /**
     * Set the header style
     *
     * @param string $format
     * @return $this
     */
    public function headerStyle(string $format)
    {
        $this->headerStyle = $format;
        return $this;
    }

    /**
     * Set the border style (the chars used for drawing the border)
     *
     * @param array $borderStyle
     * @return $this
     */
    public function borderStyle(array $borderStyle)
    {
        if (isset($borderStyle[self::CROSS])) {
            foreach ([
                self::CORNER_TOP_LEFT, self::CORNER_TOP_RIGHT, self::CORNER_BOTTOM_LEFT, self::CORNER_BOTTOM_RIGHT,
                self::TEE_HORIZONTAL_UP, self::TEE_HORIZONTAL_DOWN, self::TEE_VERTICAL_LEFT, self::TEE_VERTICAL_RIGHT,
                     ] as $key) {
                isset($borderStyle[$key]) || $borderStyle[$key] = $borderStyle[self::CROSS];
            }
        }
        $this->borderStyle = array_merge($this->borderStyle, $borderStyle);
        return $this;
    }

    /**
     * Use the keys as of the rows as header
     *
     * @return $this
     */
    public function headersFromKeys()
    {
        $this->prepareColumns();
        foreach ($this->columns as $key => $column) {
            $column->header = $key;
        }
        return $this;
    }

    /**
     * Adjust column identified by $key
     *
     * $definition may include `width`, `header`, `delete` and `format`
     *
     * @param $key
     * @param $definition
     * @return $this
     */
    public function column($key, $definition)
    {
        $this->prepareColumns();
        if (!isset($this->columns[$key])) {
            throw new \InvalidArgumentException('There is no column ' . $key . ' in our data');
        }

        foreach ($definition as $var => $value) {
            switch ($var) {
                case 'delete':
                    unset($this->columns[$key]);
                    break;
                default:
                    $this->columns[$key]->$var = $value;
            }
        }

        return $this;
    }

    /**
     * Check if headers are defined
     *
     * @return bool
     */
    public function hasHeaders()
    {
        foreach ($this->columns as $column) {
            if (isset($column->header)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Draw the table on provided console
     *
     * @todo this should start an interactive mode if requested and available
     */
    public function draw()
    {
        $this->console->line($this->getText());
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
        $this->prepareColumns();
        $rows = [];

        array_push($rows, ...$this->getRows($this->data));
        $borderRow = $this->getBorderRow();

        if ($this->bordersInside) {
            $this->repeatRow($rows, $borderRow);
        }

        if ($this->hasHeaders()) {
            $headerRow = $this->getHeaderRow();
            if ($this->repeatHeader) {
                if ($this->bordersInside) {
                    $this->repeatRow($rows, [$headerRow, $borderRow], $this->repeatHeader * 2);
                } else {
                    $this->repeatRow($rows, [$borderRow, $headerRow, $borderRow], $this->repeatHeader);
                }
            }

            if ($this->border) {
                array_unshift($rows, $borderRow);
            }
            array_unshift($rows, $headerRow);
        }

        if ($this->border) {
            array_unshift($rows, $this->getTopBorderRow());
            array_push($rows, $this->getBottomBorderRow());
        }

        return implode(PHP_EOL, $rows);
    }

    protected function prepareColumns()
    {
        if ($this->columns) {
            return;
        }

        $columns = [];
        foreach ($this->data as $row) {
            foreach ($row as $key => $value) {
                $type = gettype($value);

                if (!isset($columns[$key])) {
                    $columns[$key] = (object)['width' => 0];
                }

                switch ($type) {
                    case 'integer':
                    case 'double':
                        $width = strlen($value);
                        isset($columns[$key]->type) || $columns[$key]->type = 'number';
                        isset($columns[$key]->align) || $columns[$key]->align = 'right';
                        break;

                    case 'boolean':
                        $width = 1;
                        isset($columns[$key]->type) || $columns[$key]->type = 'boolean';
                        break;

                    case 'object':
                    case 'string':
                        $width = $this->console->strLen($value);
                        $columns[$key]->type = 'string';
                        $columns[$key]->align = 'left';
                        break;

                    case 'NULL':
                        $width = 4;
                        isset($columns[$key]->type) || $columns[$key]->type = 'null';
                        break;
                }

                if ($width > $columns[$key]->width) {
                    $columns[$key]->width = $width;
                }
            }
        }
        $this->columns = $columns;
    }

    protected function getRows(iterable $data): array
    {
        $rows = [];
        list($left, $right, $spacer) = $this->getDivider();

        foreach ($data as $row) {
            $r = [];
            foreach ($this->columns as $key => $column) {
                $fallback = null;
                if (is_object($row) && !$row instanceof \ArrayAccess && property_exists($row, $key) ||
                    (!is_object($row) || $row instanceof \ArrayAccess) && array_key_exists($key, $row)
                ) {
                    $fallback = 'null';
                }
                $value = is_object($row) && !$row instanceof \ArrayAccess ? $row->$key ?? $fallback :
                    $row[$key] ?? $fallback;

                if (is_bool($value)) {
                    $value = $value ? 't' : 'f';
                }

                if (!$value) {
                    $r[] = str_repeat(' ', $column->width);
                    continue;
                }

                $value = isset($column->format) ? sprintf($column->format, $value) : (string)$value;
                $width = $this->console->strLen($value);
                if ($width !== mb_strlen($value)) {
                    $value .= '${r}';
                }
                if ($width < $column->width) {
                    switch ($column->align ?? 'left') {
                        case 'left':
                            $value = $value . str_repeat(' ', $column->width - $width);
                            break;

                        case 'right':
                            $value = str_repeat(' ', $column->width - $width) . $value;
                            break;

                        case 'center':
                            $value = str_repeat(' ', ceil(($column->width - $width) / 2)) . $value .
                                   str_repeat(' ', floor(($column->width - $width) / 2));
                            break;
                    }
                }
                $r[] = $value;
            }
            $rows[] = $left . implode($spacer, $r) . $right;
        }

        return $rows;
    }

    protected function getHeaderRow()
    {
        list($left, $right, $spacer) = $this->getDivider();

        $r = [];
        foreach ($this->columns as $column) {
            if (!isset($column->header)) {
                $r[] = str_repeat(' ', $column->width);
                continue;
            }

            $value = $column->header;
            $width = $this->console->strLen($value);
            if ($width > $column->width) {
                $value = substr($value, 0, $column->width - $width - 1) . '…';
                $width = $this->console->strLen($value);
            }
            $r[] = $this->headerStyle . $value . str_repeat(' ', $column->width - $width) . '${r}';
        }

        return $left . implode($spacer, $r) . $right;
    }

    protected function getTopBorderRow()
    {
        $r = [];
        foreach ($this->columns as $column) {
            $r[] = str_repeat($this->borderStyle[self::BORDER_HORIZONTAL], $column->width + $this->padding * 2);
        }

        return $this->borderStyle[self::CORNER_TOP_LEFT] .
               implode($this->borderStyle[self::TEE_HORIZONTAL_DOWN], $r) .
               $this->borderStyle[self::CORNER_TOP_RIGHT];
    }

    protected function getBottomBorderRow()
    {
        $r = [];
        foreach ($this->columns as $column) {
            $r[] = str_repeat($this->borderStyle[self::BORDER_HORIZONTAL], $column->width + $this->padding * 2);
        }

        return $this->borderStyle[self::CORNER_BOTTOM_LEFT] .
               implode($this->borderStyle[self::TEE_HORIZONTAL_UP], $r) .
               $this->borderStyle[self::CORNER_BOTTOM_RIGHT];
    }

    protected function getBorderRow()
    {
        list($left, $right, $spacer) = $this->getDivider(
            $this->borderStyle[self::BORDER_HORIZONTAL],
            $this->borderStyle[self::TEE_VERTICAL_RIGHT],
            $this->borderStyle[self::TEE_VERTICAL_LEFT],
            $this->borderStyle[self::CROSS]
        );

        $r = [];
        foreach ($this->columns as $column) {
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
        if ($this->border) {
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

    protected function applyDefaultStyle()
    {
        foreach (self::$defaultStyle as $attribute => $value) {
            $this->$attribute = $value;
        }
    }
}
