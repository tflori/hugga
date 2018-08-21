<?php

namespace Hugga;

interface InputInterface
{
    public function readLine();

    public function read(int $count);

    public function readUntil(string $sequence);
}
