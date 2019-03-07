<?php
namespace Sx\Container;

use Psr\Container\ContainerInterface;

/**
 * A very simple implementation of the corresponding PSR interface.
 * This can be used as standalone or base for other containers like done with the Injector in this package.
 *
 * @package Sx\Container
 */
class Container implements ContainerInterface
{
    /**
     * This property holds the data as simple key/ value pairs.
     *
     * @var array
     */
    private $stack = [];

    /**
     * Returns the value for the given ID and throws an exception if the value was not set before.
     *
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundException
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException(sprintf('%s: unable to get %s', \get_class($this), $id));
        }
        return $this->stack[$id];
    }

    /**
     * Checks if a value was set for the given ID.
     *
     * @param string $id
     *
     * @return bool
     */
    public function has($id): bool
    {
        return isset($this->stack[$id]);
    }

    /**
     * Sets an arbitrary value for the given ID.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function set(string $id, $value): void
    {
        $this->stack[$id] = $value;
    }
}
