<?php
namespace App\Action;

use App\MiddlewareAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class PasswordList extends MiddlewareAbstract
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $list = [];
        for ($id = 1; $id <= 5; $id ++) {
            $list[] = [
                'id' => $id
            ];
        }
        return $this->helper->create(200, $list);
    }
}
