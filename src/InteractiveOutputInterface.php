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
}
