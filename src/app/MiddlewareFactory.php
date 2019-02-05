<?php
namespace App;

use Psr\Http\Server\MiddlewareInterface;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\Response\HelperInterface;

class MiddlewareFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): MiddlewareInterface
    {
        return new $class($injector->get(HelperInterface::class));
    }
}
