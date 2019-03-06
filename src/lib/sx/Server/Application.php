<?php
namespace Sx\Server;

use Psr\Http\Message\ServerRequestInterface;

/**
 * The main application handling the middleware dispatching of the chain and the response output.
 *
 * @package Sx\Server
 */
class Application implements ApplicationInterface
{
    /**
     * The handler which allows to chain a middleware and acts as a handler proxy for the next middleware.
     *
     * @var MiddlewareHandler
     */
    private $handler;

    /**
     * Creates a new Application with the middleware chain handler.
     * The middleware chain should be added by the app specific factory.
     *
     * @param MiddlewareHandler $handler
     */
    public function __construct(MiddlewareHandler $handler)
    {
        // Clone the handler to not have side effects from outside code also using the handler.
        $this->handler = clone $handler;
    }

    /**
     * Dispatches the added middleware using the chain handler from the constructor.
     * Be sure to chain an error handler first and a not found handler last before using run. Otherwise you risk
     * exceptions preventing the output to be executed.
     *
     * @param ServerRequestInterface $request
     */
    public function run(ServerRequestInterface $request): void
    {
        // Let the chain handler handle the request it will call the first middleware and provide the next handler.
        $response = $this->handler->handle($request);
        // Output the acquired response to the client.
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $key => $value) {
            // Do not use getHeaderLine here since all needed data is already available in the loop.
            header($key . ': ' . implode(',', $value));
        }
        echo $response->getBody();
    }

    /**
     * Adds a new middleware to the chain. This is a proxy to the middleware chain handler from the constructor
     * It is not intended to fill the chain handler directly as it only acts as a dispatch helper also used in routing.
     *
     * @param string $middleware
     */
    public function add(string $middleware): void
    {
        $this->handler->chain($middleware);
    }
}
