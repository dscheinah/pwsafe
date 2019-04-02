<?php
namespace App\Action;

use Psr\Http\Message\StreamFactoryInterface;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Message\Response\HelperInterface;

/**
 * Factory to create the generate action.
 *
 * @package App\Action
 */
class GenerateFactory implements FactoryInterface
{
    /**
     * Creates the generate action giving the dictionary in addition to the response helper.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return Generate
     */
    public function create(Injector $injector, array $options, string $class) : Generate
    {
        /* @var StreamFactoryInterface $factory */
        $factory = $injector->get(StreamFactoryInterface::class);
        return new Generate(
            $injector->get(HelperInterface::class),
            $factory->createStreamFromFile(\dirname(__DIR__, 2) . '/data/dictionary.txt')
        );
    }
}
