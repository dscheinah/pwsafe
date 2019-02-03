<?php
namespace App\Middleware;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class NotFoundHandlerFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options = []): NotFoundHandler
    {
        return new NotFoundHandler();
    }
}
