<?php
namespace Sx\Data;

/**
 * Provides all basic methods to access the PHP session array in an object oriented and controlled way.
 * The get and set methods are implemented according to Containers.
 *
 * @package Sx\Data
 */
class Session implements SessionInterface
{
    /**
     * The scope is used as first level separator inside the global session array.
     *
     * @var string
     */
    protected $scope;

    /**
     * The options given to PHP ini_set before starting the session.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Creates a new session wrapper with a given scope. By using different scopes different instances can be separated
     * to prevent accidental overwriting of values. The options are key/ value pairs given to session_start.
     * The session cannot be used before calling start exactly once on any instance.
     *
     * @param string $scope
     * @param array  $options
     */
    public function __construct(string $scope, array $options = [])
    {
        $this->scope = $scope;
        $this->options = $options;
    }

    /**
     * Retrieves a value from the session array in the current scope.
     *
     * @param string $id
     *
     * @return mixed
     * @throws SessionException
     */
    public function get($id)
    {
        if (!isset($_SESSION[$this->scope][$id])) {
            throw new SessionException(sprintf('key %s not found in session %s', $id, $this->scope));
        }
        return $_SESSION[$this->scope][$id];
    }

    /**
     * Checks if a value is existent within the current scope.
     *
     * @param string $id
     *
     * @return bool
     */
    public function has($id): bool
    {
        return isset($_SESSION[$this->scope][$id]);
    }

    /**
     * Sets a value into the current scope to be used with get or has.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function set(string $id, $value): void
    {
        $_SESSION[$this->scope][$id] = $value;
    }

    /**
     * Tries to start the session with the provided options.
     * Multiple instances cannot be started in parallel. So always use end if the session is not needed anymore.
     *
     * @throws SessionException
     */
    public function start(): void
    {
        if (!session_start($this->options)) {
            throw new SessionException('failed to start session');
        }
    }

    /**
     * Closes the session and write the data to the backend registered within PHP.
     * Afterwards the session instance (or another one) can be started again.
     */
    public function end(): void
    {
        session_write_close();
    }
}
