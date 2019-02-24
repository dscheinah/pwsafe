<?php
namespace Sx\Data;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SessionException extends \Exception implements ContainerExceptionInterface, NotFoundExceptionInterface
{
}
