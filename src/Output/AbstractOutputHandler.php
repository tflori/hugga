<?php

namespace Hugga\Output;

use Hugga\AbstractHandler;
use Hugga\OutputHandlerInterface;

abstract class AbstractOutputHandler extends AbstractHandler implements OutputHandlerInterface
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
