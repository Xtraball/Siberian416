<?php

/**
 * Class Siberian_Exception
 *
 * @version 4.13.20
 *
 */
class Siberian_Exception extends Exception
{
    /**
     * Siberian_Exception constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        log_exception($this);
    }
}
