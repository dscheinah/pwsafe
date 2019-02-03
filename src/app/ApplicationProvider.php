<?php
namespace App;

use App\Middleware\ErrorHandler;
use App\Middleware\ErrorHandlerFactory;
use App\Middleware\NotFoundHandler;
use App\Middleware\NotFoundHandlerFactory;
use Sx\Container\Injector;
use Sx\Container\ProviderInterface;
use Sx\Message\ResponseFactory;
use Sx\Server\ApplicationInterface;
use Sx\Server\RouterInterface;
use Sx\Message\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Server\MiddlewareHandlerFactory;

class ApplicationProvider implements ProviderInterface
{

    public function provide(Injector $injector): void
    {
        $injector->set(ApplicationInterface::class, ApplicationFactory::class);
        $injector->set(ServerRequestInterface::class, ServerRequestFactory::class);
        $injector->set(MiddlewareHandlerInterface::class, MiddlewareHandlerFactory::class);
        $injector->set(ResponseFactory::class, ResponseFactory::class);
        $injector->set(ErrorHandler::class, ErrorHandlerFactory::class);
        $injector->set(RouterInterface::class, RouterFactory::class);
        $injector->set(NotFoundHandler::class, NotFoundHandlerFactory::class);
    }
}
