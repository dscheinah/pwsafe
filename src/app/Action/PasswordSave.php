<?php
namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PasswordSave extends Password
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->prepareRepo($request);
        return $handler->handle($request->withAttribute('id', $this->repo->savePassword($request->getAttributes())));
    }
}
