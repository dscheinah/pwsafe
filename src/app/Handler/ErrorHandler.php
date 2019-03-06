<?php
namespace App\Handler;

use App\MiddlewareAbstract;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The error handler to create error responses out of exceptions.
 *
 * @package App\Handler
 */
class ErrorHandler extends MiddlewareAbstract
{
    /**
     * Wraps the call to the next handler in a try/ catch block and creates error responses.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Exception $e) {
            return $this->helper->create(
                $e->getCode(),
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
}
