<?php
namespace Sx\Data;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Used to indicate all session errors. This includes various and "not found" errors.
 *
 * @package Sx\Data
 */
class SessionException extends \Exception implements ContainerExceptionInterface, NotFoundExceptionInterface
{
}
