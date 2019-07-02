<?php

namespace Laravel\Spark\Exceptions;

use Exception;

class NotSupportedByMollie extends Exception
{
    /**
     * Create a new exception for the given reason.
     *
     * @param  string  $reason
     * @return static
     */
    public static function because($reason)
    {
        return new static($reason);
    }

    /**
     * Create a new exception for the given reason.
     *
     * @param $unsupportedMethod
     * @return static
     */
    public static function becauseOfMethod($unsupportedMethod)
    {
        $reason = sprintf('Method %s() not supported when billing with Mollie.', $unsupportedMethod);

        return static::because($reason);
    }
}
