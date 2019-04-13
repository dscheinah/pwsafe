<?php
namespace App\Model;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Utility\LogInterface;

/**
 * Factory for the GroupRepo.
 *
 * @package App\Model
 */
class GroupRepoFactory implements FactoryInterface
{
    /**
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return GroupRepo
     */
    public function create(Injector $injector, array $options, string $class): GroupRepo
    {
        return new GroupRepo($injector->get(LogInterface::class), $injector->get(GroupStorage::class));
    }
}
