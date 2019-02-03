<?php
namespace App\Middleware;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\ResponseFactory;
use Psr\Http\Message\ResponseFactoryInterface;

class ErrorHandlerFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options = []): ErrorHandler
    {
        return new ErrorHandler($injector->get(ResponseFactoryInterface::class));
    }
}
