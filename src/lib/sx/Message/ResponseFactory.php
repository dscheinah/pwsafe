<?php
namespace Sx\Message;

use Psr\Http\Message\ResponseFactoryInterface;
use Sx\Message\Response;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory implements ResponseFactoryInterface
{

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $response = new Response();
        return $response->withStatus($code, $reasonPhrase);
    }
}
