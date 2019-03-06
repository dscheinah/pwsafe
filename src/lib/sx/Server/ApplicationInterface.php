<?php
namespace Sx\Server;

use Psr\Http\Message\ServerRequestInterface;

/**
 * An interface for the core functionality of an application.
 *
 * @package Sx\Server
 */
interface ApplicationInterface
{
    /**
     * Must run the dispatch of the request and output the response to the client.
     *
     * @param ServerRequestInterface $request
     */
    public function run(ServerRequestInterface $request): void;

    /**
     * Must be implemented to add a new middleware to the dispatch process.
     * The middleware is only given as a class name to allow lazy class loading.
     *
     * @param string $middleware
     */
    public function add(string $middleware): void;
}
