<?php
namespace Sx\Server;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Sx\Container\Injector;
use Sx\Container\InjectorException;
use Sx\Container\NotFoundException;

/**
 * A helper class for dispatching in the application and the router.
 * It hold a middleware chain and calls it providing the next handler. It also implements lazy loading for middleware.
 *
 * @package Sx\Server
 */
class MiddlewareHandler implements MiddlewareHandlerInterface
{
    /**
     * The Injector to be used for lazy loading.
     *
     * @var Injector
     */
    private $injector;

    /**
     * The list of added middleware classes.
     *
     * @var string[]
     */
    private $stack = [];

    /**
     * Creates a new middleware handler providing the Injector for lazy loading of chained middleware classes.
     *
     * @param Injector $injector
     */
    public function __construct(Injector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * Dispatches the request with the current middleware chain and returns the response.
     * If no response was generated an exception is thrown.
     * This exception is only useful for dispatching inside the main chain like with routing.
     * Be sure to always add a middleware to the main chain returning a response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws MiddlewareHandlerException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $next = $this->next();
        } catch (InjectorException | NotFoundException $e) {
            // If lazy loading failed there is some misconfiguration which breaks the chain.
            $next = null;
        }
        // If the next handler is called the middleware did not create a response.
        // If no next is available, this is an error.
        if (! $next) {
            throw new MiddlewareHandlerException('no middleware returned a response', 501);
        }
        // Use this instance as a next handler, which results in indirect recursion when the middleware calls handle.
        return $next->process($request, $this);
    }

    /**
     * Add a new middleware to the chain. This is only given as a string to provide lazy loading.
     *
     * @param string $middleware
     */
    public function chain(string $middleware): void
    {
        $this->stack[] = $middleware;
    }

    /**
     * Lazy loads the the next middleware.
     *
     * @return MiddlewareInterface|null
     * @throws InjectorException
     * @throws NotFoundException
     */
    private function next(): ?MiddlewareInterface
    {
        // Use simple array iterator functions to store the current iteration state.
        $middleware = current($this->stack);
        next($this->stack);
        // Lazy load the class.
        return $middleware ? $this->injector->get($middleware) : null;
    }
}
