<?php
namespace App\Action;

use Sx\Container\FactoryInterface;
use App\Model\PasswordRepo;
use Sx\Container\Injector;
use Sx\Message\Response\HelperInterface;

class PasswordFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): Password
    {
        return new $class($injector->get(HelperInterface::class), $injector->get(PasswordRepo::class));
    }
}
