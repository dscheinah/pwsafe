<?php
namespace Sx\Message;

use Psr\Http\Message\ResponseInterface;

class Response extends Message implements ResponseInterface
{

    protected $statusCode = 200;

    protected $statusReason = '';

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $response = clone $this;
        $response->statusCode = $code;
        $response->statusReason = $reasonPhrase;
        return $response;
    }

    public function getReasonPhrase()
    {
        return $this->statusReason;
    }
}
