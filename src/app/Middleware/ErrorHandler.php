<?php
namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sx\Message\ResponseFactory;

class ErrorHandler implements MiddlewareInterface
{

    private $response;

    public function __construct(ResponseFactory $response)
    {
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Exception $e) {
            return $this->response->createResponse($e->getCode());
        }
    }
}
