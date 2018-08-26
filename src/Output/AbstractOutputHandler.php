<?php

namespace Hugga\Output;

use Hugga\AbstractHandler;
use Hugga\OutputInterface;

abstract class AbstractOutputHandler extends AbstractHandler implements OutputInterface
{
    public static function isCompatible($resource)
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
