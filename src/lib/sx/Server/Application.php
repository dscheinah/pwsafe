<?php
namespace Sx\Server;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;

class Application implements ApplicationInterface
{

    private $request;

    private $handler;

    public function __construct(ServerRequestFactoryInterface $request, MiddlewareHandler $handler)
    {
        $this->request = $request;
        $this->handler = $handler;
    }

    public function run(string $method, string $uri, array $server): void
    {
        $response = $this->handler->handle($this->request->createServerRequest($method, $uri, $server));
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $key => $value) {
            header($key . ': ' . implode(',', $value));
        }
        echo $response->getBody();
    }

    public function add(MiddlewareInterface $middleware): void
    {
        $this->handler->chain($middleware);
    }
}
