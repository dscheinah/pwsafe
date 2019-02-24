<?php
namespace App\Action;

use App\MiddlewareAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class PasswordSave extends MiddlewareAbstract
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->helper->create(200, [
            'id' => (int) $request->getAttribute('id', rand(10, 100)),
            'name' => $request->getAttribute('name'),
            'url' => $request->getAttribute('url'),
            'user' => $request->getAttribute('user'),
            'email' => $request->getAttribute('email'),
            'password' => $request->getAttribute('password'),
            'notice' => $request->getAttribute('notice')
        ]);
    }
}
