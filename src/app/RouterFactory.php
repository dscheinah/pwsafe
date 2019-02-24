<?php
namespace App;

use App\Action\Password;
use App\Action\PasswordList;
use App\Action\PasswordSave;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Server\Router;
use Sx\Server\RouterInterface;

class RouterFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): RouterInterface
    {
        $router = new Router($injector->get(MiddlewareHandlerInterface::class));

        $router->get('/password', Password::class);
        $router->get('/passwords', PasswordList::class);

        $router->post('/password', PasswordSave::class);

        return $router;
    }
}
