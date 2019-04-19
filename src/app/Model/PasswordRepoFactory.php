<?php
namespace App\Model;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Utility\LogInterface;

/**
 * Factory for the PasswordRepo domain.
 *
 * @package App\Model
 */
class PasswordRepoFactory implements FactoryInterface
{
    /**
     * Creates the domain repository with the according database storage.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return PasswordRepo
     */
    public function create(Injector $injector, array $options, string $class): PasswordRepo
    {
        return new PasswordRepo(
            $injector->get(LogInterface::class),
            $injector->get(PasswordStorage::class),
            $injector->get(CategoryStorage::class),
            $injector->get(GroupStorage::class),
            $injector->get(UserStorage::class)
        );
    }
}
