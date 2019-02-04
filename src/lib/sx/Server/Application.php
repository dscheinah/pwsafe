<?php
namespace Sx\Server;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class Application implements ApplicationInterface
{

    private $handler;

    public function __construct(MiddlewareHandler $handler)
    {
        $this->handler = $handler;
    }

    public function run(ServerRequestInterface $request): void
    {
        $response = $this->handler->handle($request);
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
