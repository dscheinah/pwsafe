<?php
namespace Sx\Server;

use Psr\Http\Server\MiddlewareInterface;

interface ApplicationInterface
{

    public function run(string $method, string $uri, array $server): void;

    public function add(MiddlewareInterface $middleware): void;
}
