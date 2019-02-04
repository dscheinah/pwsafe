<?php
namespace App;

use App\Handler\ErrorHandler;
use App\Handler\ErrorHandlerFactory;
use App\Handler\NotFoundHandler;
use App\Handler\NotFoundHandlerFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Sx\Container\Injector;
use Sx\Container\ProviderInterface;
use Sx\Message\ResponseFactory;
use Sx\Server\ApplicationInterface;
use Sx\Server\MiddlewareHandlerFactory;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Server\RouterInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Sx\Message\StreamFactory;

class ApplicationProvider implements ProviderInterface
{

    public function provide(Injector $injector): void
    {
        $injector->set(ApplicationInterface::class, ApplicationFactory::class);
        $injector->set(MiddlewareHandlerInterface::class, MiddlewareHandlerFactory::class);
        $injector->set(ResponseFactoryInterface::class, ResponseFactory::class);
        $injector->set(StreamFactoryInterface::class, StreamFactory::class);
        $injector->set(ErrorHandler::class, ErrorHandlerFactory::class);
        $injector->set(RouterInterface::class, RouterFactory::class);
        $injector->set(NotFoundHandler::class, NotFoundHandlerFactory::class);
    }
}
