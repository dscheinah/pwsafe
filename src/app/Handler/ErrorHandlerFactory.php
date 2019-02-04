<?php
namespace App\Handler;

use Psr\Http\Message\ResponseFactoryInterface;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Psr\Http\Message\StreamFactoryInterface;

class ErrorHandlerFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options = []): ErrorHandler
    {
        return new ErrorHandler($injector->get(ResponseFactoryInterface::class), $injector->get(StreamFactoryInterface::class));
    }
}
