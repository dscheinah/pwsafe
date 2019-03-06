<?php
namespace Sx\Container;

use Psr\Container\ContainerExceptionInterface;

/**
 * A special container exception for the Injector. It usually does indicate a programming or configuration error.
 * It is a RuntimeException to indicate this.
 *
 * @package Sx\Container
 */
class InjectorException extends \RuntimeException implements ContainerExceptionInterface
{
}
