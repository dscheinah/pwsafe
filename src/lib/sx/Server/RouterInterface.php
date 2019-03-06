<?php
namespace Sx\Server;

use Psr\Http\Server\MiddlewareInterface;

/**
 * A router should have behave as a middleware and supply the functions defined in this interface.
 *
 * @package Sx\Server
 */
interface RouterInterface extends MiddlewareInterface
{
    /**
     * Must add a middleware for the given path and the request method GET.
     *
     * @param string $path
     * @param string $middleware
     */
    public function get(string $path, string $middleware): void;

    /**
     * Must add a middleware for the given path and the request method POST.
     *
     * @param string $path
     * @param string $middleware
     */
    public function post(string $path, string $middleware): void;

    /**
     * Must add a middleware for the given path and the request method DELETE.
     *
     * @param string $path
     * @param string $middleware
     */
    public function delete(string $path, string $middleware): void;

    /**
     * Must add a middleware for the given path and the request method PUT.
     *
     * @param string $path
     * @param string $middleware
     */
    public function put(string $path, string $middleware): void;

    /**
     * Must add a middleware for the given path and the request method HEAD.
     *
     * @param string $path
     * @param string $middleware
     */
    public function head(string $path, string $middleware): void;

    /**
     * Must add a middleware for the given path and the request method OPTIONS.
     *
     * @param string $path
     * @param string $middleware
     */
    public function options(string $path, string $middleware): void;

    /**
     * Must add a middleware for the given path and the request method PATCH.
     *
     * @param string $path
     * @param string $middleware
     */
    public function patch(string $path, string $middleware): void;
}
