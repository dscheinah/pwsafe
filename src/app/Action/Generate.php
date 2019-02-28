<?php
namespace App\Action;

use App\MiddlewareAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class Generate extends MiddlewareAbstract
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $targetLength = $request->getAttribute('length', 20);
        $password = '';
        $length = 0;
        while ($length < $targetLength) {
            $password .= preg_replace('/[^\x21-\x7E]/', '', random_bytes($targetLength - $length));
            $length = strlen($password);
        }
        return $this->helper->create(200, [
            'password' => $password
        ]);
    }
}
