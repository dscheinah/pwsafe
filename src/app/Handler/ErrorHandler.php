<?php
namespace App\Handler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\StreamFactoryInterface;

class ErrorHandler implements MiddlewareInterface
{

    private $response;

    private $stream;

    public function __construct(ResponseFactoryInterface $response, StreamFactoryInterface $stream)
    {
        $this->response = $response;
        $this->stream = $stream;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Exception $e) {
            $response = $this->response->createResponse($e->getCode());
            return $response->withBody($this->stream->createStream($e->getMessage()));
        }
    }
}
