<?php
namespace Sx\Container;

/**
 * This containers handles dependency injection. It must be manually created once in the early startup phase.
 *
 * @package Sx\Container
 */
class Injector extends Container
{
    /**
     * To provide singleton like instances this cache stores all created objects.
     *
     * @var array
     */
    private $instances = [];

    /**
     * Collects IDs from calls to multiple.
     *
     * @var array
     */
    private $multiple = [];

    /**
     * Holds the global configuration state for this Injector which is given as is to all factories.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Creates a new Injector to handle global dependency injection.
     * The provided options are forwarded to all factories as the second parameter of create.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Creates a new instance for the given class name. This class must have been registered using set. The stored
     * value from set will be used as a class name. If the instantiated class implements the FactoryInterface,
     * it's create function will be called. If not the class will be returned as is.
     * For any error happening while instance creation or if the class is not found, an exception is thrown.
     * Each object will only be created once if not indicated otherwise by using multiple.
     *
     * @param string $id
     *
     * @return mixed
     * @throws InjectorException
     * @throws NotFoundException
     */
    public function get($id)
    {
        // Use the already created instance if available and not marked with multiple.
        if (!($this->multiple[$id] ?? false) && isset($this->instances[$id])) {
            return $this->instances[$id];
        }
        // Get the plain class name from the basic container.
        $class = parent::get($id);
        // If it is a real class name it can be created.
        if (\is_string($class) && class_exists($class)) {
            try {
                // Create the factory and use it's create method if implementing the FactoryInterface.
                $factory = new $class();
                if ($factory instanceof FactoryInterface) {
                    return $this->instances[$id] = $factory->create($this, $this->options, $id);
                }
                // If not it is assumed that no factory is needed. So just return the created instance.
                return $this->instances[$id] = $factory;
            } catch (\Exception $e) {
                throw new InjectorException($e->getMessage(), $e->getCode(), $e);
            }
        }
        // The value from set will not be returned if it is not an object. Actually code should not reach this line.
        if (!\is_object($class)) {
            throw new InjectorException(sprintf('instance for %s could not be created', $id));
        }
        return $this->instances[$id] = $class;
    }

    /**
     * Use this method to indicate that the object given ID (the same used in set and get) should always be created.
     * So no singleton mechanism will be applied when using get. This does not prevent the instance cache to be used.
     * So never rely on object destruction before the end of the script.
     *
     * @param $id
     */
    public function multiple($id): void
    {
        $this->multiple[$id] = true;
    }

    /**
     * This method can be called to apply additional calls to set and multiple for each module.
     * The module needs to define a class implementing ProviderInterface to indicate the usability for the Injector.
     *
     * @param ProviderInterface $provider
     */
    public function setup(ProviderInterface $provider): void
    {
        $provider->provide($this);
    }
}
