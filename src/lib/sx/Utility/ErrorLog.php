<?php
namespace Sx\Utility;

/**
 * A simple logger logging to the default PHP error log.
 *
 * @package Sx\Utility
 */
class ErrorLog implements LogInterface
{
    /**
     * Creates the logger and enables logging.
     */
    public function __construct()
    {
        ini_set('log_errors', 1);
    }

    /**
     * Writes a message to the error log.
     *
     * @param string $message
     */
    public function log(string $message): void
    {
        /** @noinspection ForgottenDebugOutputInspection */
        error_log($message);
    }
}