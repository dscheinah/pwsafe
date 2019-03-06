<?php
namespace App\Model;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;

/**
 * Factory for the UserRepo domain.
 *
 * @package App\Model
 */
class UserRepoFactory implements FactoryInterface
{
    /**
     * Creates the domain repository with the according database storage.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return UserRepo
     */
    public function create(Injector $injector, array $options, string $class): UserRepo
    {
        return new UserRepo($injector->get(UserStorage::class));
    }
}
