<?php
namespace App\Action;

use App\Model\UserRepo;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\Response\HelperInterface;

/**
 * Factory for all user actions.
 *
 * @package App\Action
 */
class UserFactory implements FactoryInterface
{
    /**
     * Creates the requested action with the response helper and user domain repository.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return User
     */
    public function create(Injector $injector, array $options, string $class): User
    {
        return new $class($injector->get(HelperInterface::class), $injector->get(UserRepo::class));
    }
}
