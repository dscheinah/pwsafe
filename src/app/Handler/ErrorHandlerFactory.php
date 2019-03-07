<?php
namespace App\Handler;

use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\Response\HelperInterface;

/**
 * The factory to create the error handler.
 *
 * @package App\Handler
 */
class ErrorHandlerFactory implements FactoryInterface
{
    /**
     * Creates the error handler with the env provided by the global config.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return ErrorHandler
     */
    public function create(Injector $injector, array $options, string $class): ErrorHandler
    {
        return new ErrorHandler($injector->get(HelperInterface::class), $options['env'] ?? '');
    }
}
