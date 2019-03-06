<?php
namespace App\Handler;

use App\MiddlewareAbstract;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A very simple not found handler to be used at the end of a handle chain in the application.
 *
 * @package App\Handler
 */
class NotFoundHandler extends MiddlewareAbstract
{
    /**
     * Simply returns a not found response.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->helper->create(404, ['message' => 'not found']);
    }
}
