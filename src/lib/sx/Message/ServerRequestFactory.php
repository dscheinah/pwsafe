<?php
namespace Sx\Message;

use Psr\Http\Message\ServerRequestInterface;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Psr\Http\Message\ServerRequestFactoryInterface;

class ServerRequestFactory implements FactoryInterface, ServerRequestFactoryInterface
{

    public function create(Injector $injector, array $options = []): ServerRequestInterface
    {
        return $this->createServerRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER);
    }

    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new ServerRequest();
    }
}
