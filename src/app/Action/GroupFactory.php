<?php
namespace App\Action;

use App\Model\GroupRepo;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\Response\HelperInterface;

/**
 * The factory for all group actions.
 *
 * @package App\Action
 */
class GroupFactory implements FactoryInterface
{
    /**
     * Creates a group related action based on the base group action.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return Group
     */
    public function create(Injector $injector, array $options, string $class): Group
    {
        return new $class($injector->get(HelperInterface::class), $injector->get(GroupRepo::class));
    }
}
