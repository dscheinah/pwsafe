<?php
namespace App;

use Sx\Server\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Data\SessionInterface;
use Sx\Server\MiddlewareHandlerException;

class Auth extends Router
{

    private $session;

    public function __construct(MiddlewareHandlerInterface $handler, SessionInterface $session)
    {
        parent::__construct($handler);
        $this->session = $session;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->session->start();
        try {
            $uri = $request->getUri();
            $routeHandler = $this->getHandler($request->getMethod(), $uri->getPath());
            if ($routeHandler) {
                return $routeHandler->handle($request);
            }
        } catch (MiddlewareHandlerException $e) {
            if (! $this->session->has('login') || ! $request->getAttribute('key')) {
                throw new AuthException('please /login and provide the key', 403);
            }
        } finally {
            $this->session->end();
        }
        return $handler->handle($request);
    }
}
