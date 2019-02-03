<?php
namespace Sx\Container;

class Injector extends Container
{

    private $instances = [];

    protected $options = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function get($id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }
        $class = parent::get($id);
        if (is_string($class) && class_exists($class)) {
            try {
                $factory = new $class();
                if ($factory instanceof FactoryInterface) {
                    return $this->instances[$id] = $factory->create($this, $this->options);
                }
                return $this->instances[$id] = $factory;
            } catch (\Exception $e) {
                throw new ContainerException($e->getMessage(), $e->getCode(), $e);
            }
        }
        if (! is_object($class)) {
            throw new ContainerException(sprintf('instance for %s could not be created'), 500);
        }
        return $this->instances[$id] = $class;
    }

    public function setup(ProviderInterface $provider): void
    {
        $provider->provide($this);
    }
}