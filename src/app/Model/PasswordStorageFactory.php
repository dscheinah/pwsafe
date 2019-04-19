<?php
namespace App\Model;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Data\BackendInterface;
use Sx\Data\Storage;

/**
 * The factory for the password storage.
 *
 * @package App\Model
 */
class PasswordStorageFactory implements FactoryInterface
{
    /**
     * Creates the password storage with the backend and the encryption key for shared passwords from the options.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return Storage
     * @throws \RuntimeException
     */
    public function create(Injector $injector, array $options, string $class): Storage
    {
        if (!($options['key'] ?? false)) {
            throw new \RuntimeException('a key to be used for encryption of shared passwords must be configured');
        }
        return new PasswordStorage($injector->get(BackendInterface::class), $options['key']);
    }
}
