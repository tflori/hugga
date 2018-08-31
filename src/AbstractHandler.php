<?php

namespace Hugga;

abstract class AbstractHandler
{
    /** @var resource */
    protected $resource;

    /** @var Console */
    protected $console;

    public static function isCompatible($resource): bool
    {
        return is_resource($resource);
    }

    /**
     * @param Console $console
     * @param resource $resource
     */
    public function __construct(Console $console, $resource)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException(sprintf(
                'Argument 1 passed to %s has to be of the type resource, %s given',
                __METHOD__,
                gettype($resource)
            ));
        }
        $this->resource = $resource;
        $this->console = $console;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }
}
