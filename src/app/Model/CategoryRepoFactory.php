<?php
namespace App\Model;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Utility\LogInterface;

/**
 * Factory for the CategoryRepo domain.
 *
 * @package App\Model
 */
class CategoryRepoFactory implements FactoryInterface
{
    /**
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return CategoryRepo
     */
    public function create(Injector $injector, array $options, string $class): CategoryRepo
    {
        return new CategoryRepo(
            $injector->get(LogInterface::class),
            $injector->get(CategoryStorage::class),
            $injector->get(PasswordStorage::class)
        );
    }
}
