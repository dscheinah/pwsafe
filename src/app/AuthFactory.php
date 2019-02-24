<?php
namespace App;

use Sx\Container\Injector;
use App\Action\Login;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Container\FactoryInterface;
use App\Action\Generate;

class AuthFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): Auth
    {
        $auth = new Auth($injector->get(MiddlewareHandlerInterface::class));

        $auth->post('/generate', Generate::class);
        $auth->post('/login', Login::class);

        return $auth;
    }
}
