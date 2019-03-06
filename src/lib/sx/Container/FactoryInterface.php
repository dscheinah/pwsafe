<?php
namespace Sx\Container;

/**
 * This interface describes a factory usable with the Injector.
 * All factories should implement this interface to be handled automatically.
 *
 * @package Sx\Container
 */
interface FactoryInterface
{
    /**
     * This method is called to retrieve the requested object from the Injector. Use the provided Injector instance
     * to load dependencies. The options are forwarded from the global state set to the Injector at creation time.
     * To allow factories for dynamic instance creation the requested class name is also given.
     * The return type is intentionally left blank to allow implementing classes to define it's own.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return mixed
     */
    public function create(Injector $injector, array $options, string $class);
}
