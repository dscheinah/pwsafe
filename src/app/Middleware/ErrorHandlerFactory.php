<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class ErrorHandlerFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options = []): ErrorHandler
    {
        return new ErrorHandler($injector->get(ResponseFactoryInterface::class));
    }
}
