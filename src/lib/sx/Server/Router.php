<?php
namespace Sx\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class Router implements RouterInterface
{

    private $handler;

    private $handlers = [];

    public function __construct(MiddlewareHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $routeHandler = $this->getHandler($request->getMethod(), $uri->getPath());
        if ($routeHandler) {
            try {
                return $routeHandler->handle($request);
            } catch (MiddlewareHandlerException $e) {}
        }
        return $handler->handle($request);
    }

    public function get(string $path, MiddlewareInterface $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    public function post(string $path, MiddlewareInterface $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    public function delete(string $path, MiddlewareInterface $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    public function put(string $path, MiddlewareInterface $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    public function head(string $path, MiddlewareInterface $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    public function options(string $path, MiddlewareInterface $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    public function patch(string $path, MiddlewareInterface $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    protected function getHandler(string $type, string $path): MiddlewareHandlerInterface
    {
        $type = strtolower($type);
        $path = trim($path, '/') ?: '/';
        if (! isset($this->handlers[$type][$path])) {
            $this->handlers[$type][$path] = clone $this->handler;
        }
        return $this->handlers[$type][$path];
    }
}
