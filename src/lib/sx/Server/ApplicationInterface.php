<?php
namespace Sx\Server;

use Psr\Http\Server\MiddlewareInterface;

interface ApplicationInterface
{

    public function run(): void;

    public function add(MiddlewareInterface $middleware): void;
}
