<?php
namespace App\Action;

use App\MiddlewareAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class ProfileSave extends MiddlewareAbstract
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->helper->create(200, [
            'user' => $request->getAttribute('user'),
            'email' => $request->getAttribute('email')
        ]);
    }
}
