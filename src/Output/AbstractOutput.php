<?php

namespace Hugga\Output;

use Hugga\AbstractInputOutput;
use Hugga\OutputInterface;

abstract class AbstractOutput extends AbstractInputOutput implements OutputInterface
{
    public static function isCompatible($resource): bool
    {
        if (parent::isCompatible($resource)) {
            $mode = stream_get_meta_data($resource)['mode'];
            if (strtolower($mode[0]) !== 'r') {
                return true;
            }
        }

        return false;
    }
}
