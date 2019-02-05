<?php
namespace Sx\Server;

use Psr\Http\Message\ServerRequestInterface;

interface ApplicationInterface
{

    public function run(ServerRequestInterface $request): void;

    public function add(string $middleware): void;
}
