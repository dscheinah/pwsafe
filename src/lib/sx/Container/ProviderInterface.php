<?php
namespace Sx\Container;

interface ProviderInterface
{

    public function provide(Injector $injector): void;
}
