<?php

namespace Hugga;

abstract class AbstractHandler
{
    /** @var resource */
    protected $resource;

    public static function isCompatible($resource)
    {
        return is_resource($resource);
    }

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

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }
}
