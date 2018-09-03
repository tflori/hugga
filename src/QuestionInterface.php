<?php

namespace Hugga;

interface QuestionInterface
{
    public function ask(Console $console);

    public function getDefault();
}
