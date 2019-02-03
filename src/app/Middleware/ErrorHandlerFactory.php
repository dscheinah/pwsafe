<?php
namespace App\Middleware;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\ResponseFactory;

class ErrorHandlerFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options = []): ErrorHandler
    {
        return new ErrorHandler($injector->get(ResponseFactory::class));
    }
}
