<?php
namespace Sx\Container;

use Psr\Container\ContainerExceptionInterface;

/**
 * A base exception to be used in containers for all types of errors except "not found", which has it's own exception.
 *
 * @package Sx\Container
 */
class ContainerException extends \Exception implements ContainerExceptionInterface
{
}
