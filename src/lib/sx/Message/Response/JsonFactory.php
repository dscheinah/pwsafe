<?php
namespace Sx\Message\Response;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * The factory to create the Json helper. This is needed to get the PSR factories from the Injector.
 *
 * @package Sx\Message\Response
 */
class JsonFactory implements FactoryInterface
{
    /**
     * Creates a new Json helper with a response and stream factory.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return Json
     */
    public function create(Injector $injector, array $options, string $class): Json
    {
        return new Json($injector->get(ResponseFactoryInterface::class), $injector->get(StreamFactoryInterface::class));
    }
}
