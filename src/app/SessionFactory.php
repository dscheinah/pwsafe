<?php
namespace App;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Data\Session;
use Sx\Data\SessionInterface;

class SessionFactory implements FactoryInterface
{

    public function create(Injector $injector, array $options, string $class): SessionInterface
    {
        return new Session($class, [
            'save_path' => $options['session']['save_path'] ?? '',
            'name' => 'pwsafe',
            'use_strict_mode' => true,
            'use_only_cookies' => true,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Strict'
        ]);
    }
}
