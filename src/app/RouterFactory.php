<?php
namespace App;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Server\Router;
use Sx\Server\RouterInterface;

class RouterFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options = []): RouterInterface
    {
        $router = new Router();
        return $router;
    }
}
