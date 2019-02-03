<?php
namespace Sx\Server;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class Application implements ApplicationInterface
{

    private $request;

    private $handler;

    public function __construct(ServerRequestInterface $request, MiddlewareHandler $handler)
    {
        $this->request = $request;
        $this->handler = $handler;
    }

    public function run(): void
    {
        $response = $this->handler->handle($this->request);
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
