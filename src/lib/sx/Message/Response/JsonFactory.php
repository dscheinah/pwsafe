<?php
namespace Sx\Message\Response;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class JsonFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class)
    {
        return new Json($injector->get(ResponseFactoryInterface::class), $injector->get(StreamFactoryInterface::class));
    }
}
