<?php
namespace App\Action;

use App\Model\UserRepo;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Data\SessionInterface;
use Sx\Message\Response\HelperInterface;

class LoginFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): Login
    {
        return new Login($injector->get(HelperInterface::class), $injector->get(SessionInterface::class), $injector->get(UserRepo::class));
    }
}
