<?php
namespace App;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Server\Router;
use Sx\Server\RouterInterface;
use Sx\Server\MiddlewareHandlerInterface;
use App\Action\PasswordList;

class RouterFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): RouterInterface
    {
        $router = new Router($injector->get(MiddlewareHandlerInterface::class));

        $router->get('/passwords', $injector->get(PasswordList::class));

        return $router;
    }
}
