<?php
namespace App;

use App\Middleware\ErrorHandler;
use App\Middleware\ErrorHandlerFactory;
use App\Middleware\NotFoundHandler;
use App\Middleware\NotFoundHandlerFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Sx\Container\Injector;
use Sx\Container\ProviderInterface;
use Sx\Message\ResponseFactory;
use Sx\Message\ServerRequestFactory;
use Sx\Server\ApplicationInterface;
use Sx\Server\MiddlewareHandlerFactory;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Server\RouterInterface;

class ApplicationProvider implements ProviderInterface
{

    public function provide(Injector $injector): void
    {
        $injector->set(ApplicationInterface::class, ApplicationFactory::class);
        $injector->set(ServerRequestFactoryInterface::class, ServerRequestFactory::class);
        $injector->set(MiddlewareHandlerInterface::class, MiddlewareHandlerFactory::class);
        $injector->set(ResponseFactoryInterface::class, ResponseFactory::class);
        $injector->set(ErrorHandler::class, ErrorHandlerFactory::class);
        $injector->set(RouterInterface::class, RouterFactory::class);
        $injector->set(NotFoundHandler::class, NotFoundHandlerFactory::class);
    }
}
