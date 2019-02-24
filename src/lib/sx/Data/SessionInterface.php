<?php
namespace Sx\Data;

use Psr\Container\ContainerInterface;

interface SessionInterface extends ContainerInterface
{

    public function set(string $id, $value): void;

    public function start(): void;

    public function end(): void;
}
