<?php
namespace Sx\Data;

use Psr\Container\ContainerInterface;

/**
 * Defines the session as a kind of Container with extended functions.
 *
 * @package Sx\Data
 */
interface SessionInterface extends ContainerInterface
{
    /**
     * Must be implemented to write data into the session. The value should be serializable.
     * The session must not start or close automatically. The interface provides start and end to be used outside.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function set(string $id, $value): void;

    /**
     * Must start the session and throws an exception for any error.
     *
     * @throws SessionException
     */
    public function start(): void;

    /**
     * Write and close the session to release all locks acquired in start.
     *
     * @throws SessionException
     */
    public function end(): void;
}
