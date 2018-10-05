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
     * Deletes rows and moves cursor to first row
     *
     * @param int $count
     */
    public function deleteLines(int $count);

    /**
     * Get the size of the output window
     *
     * Returns an array with [int $rows, int $cols]
     *
     * @return array
     */
    public function getSize(): array;
}
