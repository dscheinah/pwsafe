<?php
namespace App\Action;

use App\Model\UserRepo;
use Psr\Http\Server\MiddlewareInterface;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\Response\HelperInterface;

/**
 * Factory to create the action for profile related actions.
 *
 * @package App\Action
 */
class ProfileFactory implements FactoryInterface
{
    /**
     * Creates the required class with domain repository for profile handling.
     * There currently is only the ProfileSave action.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return MiddlewareInterface
     */
    public function create(Injector $injector, array $options, string $class): MiddlewareInterface
    {
        return new $class($injector->get(HelperInterface::class), $injector->get(UserRepo::class));
    }
}
