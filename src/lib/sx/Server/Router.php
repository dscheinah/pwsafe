<?php
namespace Sx\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The Router is a special middleware which create a tree chain inside the main chain. The tree chain is described
 * as a second dispatching in dependency of the requests properties.
 * This router does not implement any wildcard paths or segment handling for extra attributes.
 *
 * @package Sx\Server
 */
class Router implements RouterInterface
{
    /**
     * The template for the chain handler used to represent all possible branches of the tree chain.
     *
     * @var MiddlewareHandlerInterface
     */
    private $handler;

    /**
     * All registered chain handlers for the registered routes by method and path.
     *
     * @var MiddlewareHandlerInterface[][]
     */
    private $handlers = [];

    /**
     * Creates a new Router giving the template for the chain handler. The handler will be cloned multiple times.
     * The middleware for the request methods and paths should be added using an application specific factory.
     *
     * @param MiddlewareHandlerInterface $handler
     */
    public function __construct(MiddlewareHandlerInterface $handler)
    {
        // Initially clone the handler to not have side effects from outside code also using the handler.
        $this->handler = clone $handler;
    }

    /**
     * Checks if a registered route is matches and if yes the according tree chain will be called.
     * If no route is matches or the middleware of the tree chain did not produce a response the next handler is called.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        // Use a matching route by method and path. The handler may be empty and throw an exception.
        $routeHandler = $this->getHandler($request->getMethod(), $uri->getPath());
        try {
            return $routeHandler->handle($request);
        } catch (MiddlewareHandlerException $e) {
            // If no matching route is registered or the tree chain did not produce a response.
            return $handler->handle($request);
        }
    }

    /**
     * Add a middleware for a route by request method GET and given path.
     *
     * @param string $path
     * @param string $middleware
     */
    public function get(string $path, string $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    /**
     * Add a middleware for a route by request method POST and given path.
     *
     * @param string $path
     * @param string $middleware
     */
    public function post(string $path, string $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    /**
     * Add a middleware for a route by request method DELETE and given path.
     *
     * @param string $path
     * @param string $middleware
     */
    public function delete(string $path, string $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    /**
     * Add a middleware for a route by request method PUT and given path.
     *
     * @param string $path
     * @param string $middleware
     */
    public function put(string $path, string $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    /**
     * Add a middleware for a route by request method HEAD and given path.
     *
     * @param string $path
     * @param string $middleware
     */
    public function head(string $path, string $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    /**
     * Add a middleware for a route by request method OPTIONS and given path.
     *
     * @param string $path
     * @param string $middleware
     */
    public function options(string $path, string $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    /**
     * Add a middleware for a route by request method PATCH and given path.
     *
     * @param string $path
     * @param string $middleware
     */
    public function patch(string $path, string $middleware): void
    {
        $this->getHandler(__FUNCTION__, $path)->chain($middleware);
    }

    /**
     * Returns the middleware handler assigned to the given request method and path.
     * This one will be used to chain more middleware or to start the dispatching of the tree chain.
     * This method will clone the template from the constructor.
     *
     * @param string $type
     * @param string $path
     *
     * @return MiddlewareHandlerInterface
     */
    protected function getHandler(string $type, string $path): MiddlewareHandlerInterface
    {
        // Normalize type and path to always match (on adding and checking against the request).
        $type = strtolower($type);
        $path = trim($path, '/') ?: '/';
        if (!isset($this->handlers[$type][$path])) {
            // Clone the template since the middleware need to be separate per route.
            $this->handlers[$type][$path] = clone $this->handler;
        }
        return $this->handlers[$type][$path];
    }
}
