<?php
namespace Sx\Server;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareHandlerInterface extends RequestHandlerInterface
{

    public function chain(MiddlewareInterface $middleware): void;
}
