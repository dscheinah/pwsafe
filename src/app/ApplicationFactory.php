<?php
namespace App;

use App\Handler\ErrorHandler;
use App\Handler\NotFoundHandler;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Server\Application;
use Sx\Server\ApplicationInterface;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Server\RouterInterface;

class ApplicationFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options = []): ApplicationInterface
    {
        $app = new Application($injector->get(MiddlewareHandlerInterface::class));
        $app->add($injector->get(ErrorHandler::class));
        $app->add($injector->get(RouterInterface::class));
        $app->add($injector->get(NotFoundHandler::class));
        return $app;
    }
}
