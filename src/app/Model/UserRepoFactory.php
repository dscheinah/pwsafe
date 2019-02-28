<?php
namespace App\Model;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class UserRepoFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): UserRepo
    {
        return new UserRepo($injector->get(UserStorage::class));
    }
}
