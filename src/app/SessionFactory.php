<?php
namespace App;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Data\SessionInterface;

/**
 * Factory to create the session access wrapper.
 *
 * @package App
 */
class SessionFactory implements FactoryInterface
{
    /**
     * Creates the session with default security aware options.
     * The options can be adjusted be defining a global config entry for 'session'.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return SessionInterface
     */
    public function create(Injector $injector, array $options, string $class): SessionInterface
    {
        return new $class(
            // Use the class as scope to support multiple scopes with the same factory by just extending the session.
            $class,
            // All options are mapped to php ini settings prefixed with 'session.'.
            array_merge(
                [
                    'name' => 'pwsafe',
                    'use_strict_mode' => true,
                    'use_only_cookies' => true,
                    'cookie_httponly' => true,
                    'cookie_samesite' => 'Strict',
                ],
                $options['session'] ?? []
            )
        );
    }
}
