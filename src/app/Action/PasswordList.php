<?php
namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PasswordList extends Password
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->prepareRepo($request);
        return $this->helper->create(200, [
            'list' => $this->repo->getPasswords()
        ]);
    }
}
