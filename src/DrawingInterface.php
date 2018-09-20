<?php

namespace Hugga;

interface DrawingInterface
{
    /**
     * Get the output for this drawing.
     *
     * The drawing may include formatting and line breaks.
     * It should never change the amount of rows.
     *
     * @return string
     */
    public function getText(): string;
}
