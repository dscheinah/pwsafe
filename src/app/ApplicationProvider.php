<?php
namespace App;

use App\Action\PasswordList;
use App\Handler\ErrorHandler;
use App\Handler\NotFoundHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Sx\Container\Injector;
use Sx\Container\ProviderInterface;
use Sx\Message\ResponseFactory;
use Sx\Message\StreamFactory;
use Sx\Server\ApplicationInterface;
use Sx\Server\MiddlewareHandlerFactory;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Server\RouterInterface;
use Sx\Message\Response\HelperInterface;
use Sx\Message\Response\JsonFactory;
use App\Action\Password;

class ApplicationProvider implements ProviderInterface
{

    public function provide(Injector $injector): void
    {
        $injector->set(ApplicationInterface::class, ApplicationFactory::class);
        $injector->set(ErrorHandler::class, MiddlewareFactory::class);
        $injector->set(HelperInterface::class, JsonFactory::class);
        $injector->set(MiddlewareHandlerInterface::class, MiddlewareHandlerFactory::class);
        $injector->set(NotFoundHandler::class, MiddlewareFactory::class);
        $injector->set(Password::class, MiddlewareFactory::class);
        $injector->set(PasswordList::class, MiddlewareFactory::class);
        $injector->set(ResponseFactoryInterface::class, ResponseFactory::class);
        $injector->set(RouterInterface::class, RouterFactory::class);
        $injector->set(StreamFactoryInterface::class, StreamFactory::class);

        $injector->multiple(MiddlewareHandlerInterface::class);
    }
}
