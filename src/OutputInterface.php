<?php

namespace Hugga;

interface OutputInterface
{
    public function write(string $str);

    public function delete(int $chars);

    public function deleteLine();
}
