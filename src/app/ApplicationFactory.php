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

    public function create(Injector $injector, array $options, string $class): ApplicationInterface
    {
        $app = new Application($injector->get(MiddlewareHandlerInterface::class));

        $app->add(ErrorHandler::class);
        $app->add(Auth::class);
        $app->add(RouterInterface::class);
        $app->add(NotFoundHandler::class);

        return $app;
    }
}
