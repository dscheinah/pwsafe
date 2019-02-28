<?php
namespace Sx\Data;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

class StorageFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): StorageInterface
    {
        return new $class($injector->get(BackendInterface::class));
    }
}
