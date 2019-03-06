<?php
namespace Sx\Container;

/**
 * This interface defines a module specific configuration provider for a container. A class implementing this interface
 * can be used with the setup method of the Injector.
 * The instantiation and call to setup must be done manually in early setup.
 *
 * @package Sx\Container
 */
interface ProviderInterface
{
    /**
     * Is called on execution setup on the Injector. In this method a module should implement the default calls to get
     * and multiple for all it's classes and corresponding factories.
     *
     * @param Injector $injector
     */
    public function provide(Injector $injector): void;
}
