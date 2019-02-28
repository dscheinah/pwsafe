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

    public function create(int $code, $response, bool $htmlify = true): ResponseInterface
    {
        if ($htmlify) {
            $response = $this->htmlify($response);
        }
        $json = json_encode($response);
        if ($json === false) {
            throw new JsonException('error encoding json', 500);
        }
        return $this->responseFactory->createResponse($code)
            ->withAddedHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($json));
    }

    protected function htmlify($response)
    {
        if (is_object($response)) {
            $response = (array) $response;
        }
        if (is_array($response)) {
            foreach ($response as $key => $value) {
                $response[$key] = $this->htmlify($value);
            }
            return $response;
        }
        return htmlentities((string) $response);
    }
}
