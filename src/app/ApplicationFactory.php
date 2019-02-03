<?php
namespace App;

use App\Middleware\ErrorHandler;
use Psr\Http\Message\ServerRequestInterface;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Server\ApplicationInterface;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Server\RouterInterface;
use Sx\Server\Application;
use App\Middleware\NotFoundHandler;

class ApplicationFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options = []): ApplicationInterface
    {
        $app = new Application($injector->get(ServerRequestInterface::class), $injector->get(MiddlewareHandlerInterface::class));
        $app->add($injector->get(ErrorHandler::class));
        $app->add($injector->get(RouterInterface::class));
        $app->add($injector->get(NotFoundHandler::class));
        return $app;
    }
}
