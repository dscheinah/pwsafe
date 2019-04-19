<?php
namespace App\Action;

use App\Model\GroupRepo;
use App\Model\UserRepo;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\Response\HelperInterface;

/**
 * The factory to create the share action.
 *
 * @package App\Action
 */
class ShareFactory implements FactoryInterface
{
    /**
     * Creates the share actions.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return Share
     */
    public function create(Injector $injector, array $options, string $class): Share
    {
        return new Share(
            $injector->get(HelperInterface::class),
            $injector->get(GroupRepo::class),
            $injector->get(UserRepo::class)
        );
    }
}
