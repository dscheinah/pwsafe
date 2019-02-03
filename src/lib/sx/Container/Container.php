<?php
namespace Sx\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{

    private $stack = [];

    public function get($id)
    {
        if (! $this->has($id)) {
            throw new NotFoundException(sprintf('%s: unable to get %s', get_class($this), $id), 501);
        }
        return $this->stack[$id];
    }

    public function has($id)
    {
        return isset($this->stack[$id]);
    }

    public function set(string $id, $value): void
    {
        $this->stack[$id] = $value;
    }
}
