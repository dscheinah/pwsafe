<?php
namespace Sx\Server;

use Psr\Http\Server\MiddlewareInterface;

interface RouterInterface extends MiddlewareInterface
{

    public function get(string $path, MiddlewareInterface $middleware): void;

    public function post(string $path, MiddlewareInterface $middleware): void;

    public function delete(string $path, MiddlewareInterface $middleware): void;

    public function put(string $path, MiddlewareInterface $middleware): void;

    public function head(string $path, MiddlewareInterface $middleware): void;

    public function options(string $path, MiddlewareInterface $middleware): void;

    public function patch(string $path, MiddlewareInterface $middleware): void;
}
