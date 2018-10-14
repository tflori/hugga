<?php

namespace Hugga;

interface InteractiveOutputInterface extends OutputInterface
{
    /**
     * Replaces the amount of lines in $new with $new
     *
     * @param string $new
     */
    public function replace(string $new);

    /**
     * Deletes $count rows and replaces them with $replace
     *
     * If $replace contains more rows than $count the rows will be appended.
     *
     * @param int $count
     * @param string $replace
     */
    public function deleteLines(int $count, string $replace = '');

    /**
     * Get the size of the output window
     *
     * Returns an array with [int $rows, int $cols]
     *
     * @return array
     */
    public function getSize(): array;
}
