<?php

namespace Hugga;

interface InputInterface
{
    public function readLine(string $prompt = null): string;

    public function read(int $count = 1, string $prompt = null): string;

    public function readUntil(string $sequence, string $prompt = null): string;

    public function getResource();
}
