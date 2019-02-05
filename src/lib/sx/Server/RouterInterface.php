<?php
namespace Sx\Server;

use Psr\Http\Server\MiddlewareInterface;

interface RouterInterface extends MiddlewareInterface
{

    public function get(string $path, string $middleware): void;

    public function post(string $path, string $middleware): void;

    public function delete(string $path, string $middleware): void;

    public function put(string $path, string $middleware): void;

    public function head(string $path, string $middleware): void;

    public function options(string $path, string $middleware): void;

    public function patch(string $path, string $middleware): void;
}
