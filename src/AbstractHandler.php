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

    protected static function isTty($resource)
    {
        if (function_exists('stream_isatty')) {
            return stream_isatty($resource);
        }
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException(sprintf(
                'Argument 1 passed to %s has to be of the type resource, %s given',
                __METHOD__,
                gettype($resource)
            ));
        }

        if ('\\' === DIRECTORY_SEPARATOR) {
            $stat = @fstat($resource);
            // Check if formatted mode is S_IFCHR
            return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
        }
        return function_exists('posix_isatty') && @posix_isatty($resource);
    }
}
