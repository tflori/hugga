<?php

namespace Hugga\Input;

use Hugga\InputInterface;

abstract class AbstractInputHandler implements InputInterface
{
    /** @var resource */
    protected $resource;

    /**
     * @param resource $resource
     */
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException(sprintf(
                'Argument 1 passed to %s has to be of the type resource, %s given',
                __METHOD__,
                gettype($resource)
            ));
        }
        $this->resource = $resource;
    }
}
