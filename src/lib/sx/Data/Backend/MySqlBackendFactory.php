<?php
namespace Sx\Data\Backend;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Data\Backend\MySqlBackend;
use Sx\Data\BackendInterface;

class MySqlBackendFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): BackendInterface
    {
        return new MySqlBackend($options['mysql'] ?? []);
    }
}
