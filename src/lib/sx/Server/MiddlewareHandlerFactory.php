<?php
namespace Sx\Server;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class MiddlewareHandlerFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options = []): MiddlewareHandlerInterface
    {
        return new MiddlewareHandler();
    }
}
