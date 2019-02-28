<?php
namespace App\Model;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class PasswordRepoFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): PasswordRepo
    {
        return new PasswordRepo($injector->get(PasswordStorage::class));
    }
}
