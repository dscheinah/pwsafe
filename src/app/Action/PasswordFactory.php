<?php
namespace App\Action;

use Sx\Container\FactoryInterface;
use App\Model\PasswordRepo;
use Sx\Container\Injector;
use Sx\Message\Response\HelperInterface;

/**
 * The factory for all password related actions.
 *
 * @package App\Action
 */
class PasswordFactory implements FactoryInterface
{
    /**
     * Creates a password action with the domain repository. All these actions are based on the Password action.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return Password
     */
    public function create(Injector $injector, array $options, string $class): Password
    {
        return new $class($injector->get(HelperInterface::class), $injector->get(PasswordRepo::class));
    }
}
