<?php
namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PasswordDelete extends Password
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->prepareRepo($request);
        $id = (int) $request->getAttribute('id');
        if ($this->repo->deletePassword($id)) {
            return $this->helper->create(200, [
                'id' => $id
            ]);
        }
        return $handler->handle($request);
    }
}
