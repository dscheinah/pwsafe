<?php
namespace Sx\Server;

/**
 * This exception is thrown by the MiddlewareHandler if no middleware of the chain was able to create a response
 * or no middleware was registered. It is used for the Router to indicate when to call the next handler.
 * It should be avoided to be thrown in the main chain of the Application by adding a final "not found" handler.
 *
 * @package Sx\Server
 */
class MiddlewareHandlerException extends \RuntimeException
{
}
