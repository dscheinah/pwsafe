<?php
namespace Sx\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * This interface defines the dispatch helper used to handle middleware chains.
 * It is used in the Application for the main chain and in the Router for the tree chain.
 *
 * @package Sx\Server
 */
interface MiddlewareHandlerInterface extends RequestHandlerInterface
{
    /**
     * Must start the dispatching of the complete registered middleware chain and return the first response created.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws MiddlewareHandlerException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface;

    /**
     * Adds a middleware to the chain. The middleware is only given by class name. So the handler must implement
     * lazy loading using a dependency injector.
     *
     * @param string $middleware
     */
    public function chain(string $middleware): void;
}
