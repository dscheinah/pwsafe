<?php
namespace App\Action;

use Psr\Http\Server\MiddlewareInterface;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Data\SessionInterface;
use Sx\Message\Response\HelperInterface;

class LoginFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): MiddlewareInterface
    {
        return new Login($injector->get(HelperInterface::class), $injector->get(SessionInterface::class));
    }
}
