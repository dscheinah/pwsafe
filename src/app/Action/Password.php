<?php
namespace App\Action;

use App\MiddlewareAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class Password extends MiddlewareAbstract
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->helper->create(200, [
            'id' => $request->getAttribute('id'),
            'name' => uniqid('', false),
            'url' => uniqid('', false),
            'user' => uniqid('', false),
            'email' => uniqid('', false),
            'password' => uniqid('', false),
            'notice' => uniqid('', false)
        ]);
    }
}
