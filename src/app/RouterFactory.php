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
use App\Action\PasswordDelete;
use App\Action\ProfileSave;

class RouterFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): RouterInterface
    {
        $router = new Router($injector->get(MiddlewareHandlerInterface::class));

        $router->get('/password', Password::class);
        $router->get('/passwords', PasswordList::class);

        $router->post('/password', PasswordSave::class);
        $router->post('/password', Password::class);
        $router->post('/profile', ProfileSave::class);

        $router->delete('/password', PasswordDelete::class);

        return $router;
    }
}
