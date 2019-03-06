<?php
namespace App\Action;

use App\Model\UserRepo;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Data\Session;
use Sx\Message\Response\HelperInterface;

/**
 * Factory to create the login action.
 *
 * @package App\Action
 */
class LoginFactory implements FactoryInterface
{
    /**
     * Creates the login action middleware providing session and domain repository.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return Login
     */
    public function create(Injector $injector, array $options, string $class): Login
    {
        return new Login(
            $injector->get(HelperInterface::class),
            $injector->get(Session::class),
            $injector->get(UserRepo::class)
        );
    }
}
