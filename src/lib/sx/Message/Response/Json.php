<?php
namespace Sx\Message\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * The helper to create responses with the content type application/json. It encodes the data as json into the body.
 *
 * @package Sx\Message\Response
 */
class Json implements HelperInterface
{
    /**
     * The PSR compatible factory to create responses.
     *
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * The PSR compatible factory to create a response body.
     *
     * @var StreamFactoryInterface
     */
    protected $streamFactory;

    /**
     * Creates the helper with the factories for responses and response bodies.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param StreamFactoryInterface   $streamFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Creates a new response with the local factory. The responses Content-Type will be application/json.
     * The response parameter is json encoded and injected as response body using the local stream factory.
     * If json_encode fails an exception is thrown.
     *
     * @param int   $code
     * @param mixed $response
     *
     * @return ResponseInterface
     * @throws JsonException
     */
    public function create(int $code, $response): ResponseInterface
    {
        $json = json_encode($response);
        if ($json === false) {
            throw new JsonException('error encoding json', 500);
        }
        return $this->responseFactory->createResponse($code)
            ->withAddedHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($json));
    }
}
