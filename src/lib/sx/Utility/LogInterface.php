<?php
namespace Sx\Utility;

/**
 * An interface for a generic logger.
 *
 * @package Sx\Uitlity
 */
interface LogInterface
{
    /**
     * Must be implemented to output the message to the default priority.
     *
     * @param string $message
     */
    public function log(string $message) : void;
}