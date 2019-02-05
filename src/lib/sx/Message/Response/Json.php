<?php
namespace Sx\Message\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Json implements HelperInterface
{

    protected $responseFactory;

    protected $streamFactory;

    public function __construct(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    public function create(int $code, $response): ResponseInterface
    {
        $json = json_encode($response);
        if ($json === false) {
            throw new JsonException('error encoding json', 500);
        }
        return $this->responseFactory->createResponse($code)->withBody($this->streamFactory->createStream($json));
    }
}
