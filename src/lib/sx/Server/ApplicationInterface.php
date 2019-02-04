<?php
namespace Sx\Server;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

interface ApplicationInterface
{

    public function run(ServerRequestInterface $request): void;

    public function add(MiddlewareInterface $middleware): void;
}
