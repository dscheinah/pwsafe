<?php
namespace App\Action;

use App\Model\CategoryRepo;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\Response\HelperInterface;

/**
 * The factory for all category actions.
 *
 * @package App\Action
 */
class CategoryFactory implements FactoryInterface
{
    /**
     * Creates a category related action based on the base category action.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return Category
     */
    public function create(Injector $injector, array $options, string $class): Category
    {
        return new $class($injector->get(HelperInterface::class), $injector->get(CategoryRepo::class));
    }
}
