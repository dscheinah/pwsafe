<?php
namespace Sx\Container;

interface FactoryInterface
{

    public function create(Injector $injector, array $options = []);
}
