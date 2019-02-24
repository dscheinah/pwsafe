<?php
namespace Sx\Data;

class Session implements SessionInterface
{

    protected $scope;

    protected $options = [];

    public function __construct(string $scope, array $options = [])
    {
        $this->scope = $scope;
        $this->options = $options;
    }

    public function get($id)
    {
        if (! isset($_SESSION[$this->scope][$id])) {
            throw new SessionException(sprintf('key %s not found in session %s', $id, $this->scope), 404);
        }
        return $_SESSION[$this->scope][$id];
    }

    public function has($id)
    {
        return isset($_SESSION[$this->scope][$id]);
    }

    public function set(string $id, $value): void
    {
        $_SESSION[$this->scope][$id] = $value;
    }

    public function start(): void
    {
        if (! session_start($this->options)) {
            throw new SessionException('failed to start session', 500);
        }
    }

    public function end(): void
    {
        session_write_close();
    }
}
