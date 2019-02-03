<?php
namespace Sx\Server;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class MiddlewareHandler implements MiddlewareHandlerInterface
{

    private $stack = [];

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $next = $this->next();
        if (! $next) {
            throw new MiddlewareHandlerException('no middleware returned a response', 501);
        }
        return $next->process($request, $this);
    }

    public function chain(MiddlewareInterface $middleware): void
    {
        $this->stack[] = $middleware;
    }

    private function next(): ?MiddlewareInterface
    {
        $middleware = current($this->stack);
        next($this->stack);
        return $middleware ?: null;
    }
}
