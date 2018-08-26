<?php

namespace Hugga;

interface OutputHandlerInterface
{
    public function write(string $str);

    public function delete(int $count);

    public function deleteLine();

    public function getResource();
}
