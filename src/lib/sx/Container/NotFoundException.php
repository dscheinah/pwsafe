<?php
namespace Sx\Container;

use Psr\Container\NotFoundExceptionInterface;

/**
 * This exception is thrown by a Container if the value to get was not set. It is always avoidable by checking has
 * before retrieving the value. To indicate this the exception is an instance of \RuntimeException.
 *
 * @package Sx\Container
 */
class NotFoundException extends \RuntimeException implements NotFoundExceptionInterface
{
}
