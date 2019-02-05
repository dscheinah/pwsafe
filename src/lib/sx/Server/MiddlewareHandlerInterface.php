<?php
namespace Sx\Server;

use Psr\Http\Server\RequestHandlerInterface;

interface MiddlewareHandlerInterface extends RequestHandlerInterface
{

    public function chain(string $middleware): void;
}
