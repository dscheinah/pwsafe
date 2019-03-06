<?php
namespace Sx\Server;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

/**
 * The factory for the middleware chain handler/ dispatch helper.
 *
 * @package Sx\Server
 */
class MiddlewareHandlerFactory implements FactoryInterface
{
    /**
     * Creates the middleware handler giving the Injector to allow lazy loading of middleware.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return MiddlewareHandlerInterface
     */
    public function create(Injector $injector, array $options, string $class): MiddlewareHandlerInterface
    {
        return new MiddlewareHandler($injector);
    }
}
