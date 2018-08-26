<?php

namespace Hugga;

interface OutputInterface
{
    public function write(string $str);

    public function delete(int $count);

    public function deleteLine();
}
