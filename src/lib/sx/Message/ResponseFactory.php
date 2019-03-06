<?php
namespace Sx\Message;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A PSR compatible factory to create responses. It is not meant to be used as a factory for the Injector.
 * This class extends the Response to be able to set the protected properties without using separate setters or clones.
 *
 * @package Sx\Message
 */
class ResponseFactory extends Response implements ResponseFactoryInterface
{
    /**
     * Creates a new response with the given code and reason phrase.
     * This function does not implement a default mapping for reason phrases.
     *
     * @param int    $code
     * @param string $reasonPhrase
     *
     * @return ResponseInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $response = new Response();
        // Access the protected properties to avoid implementing setters or cloning by calling with...
        // This works as the factory extends the Response just for this reason.
        $response->statusCode = $code;
        $response->statusReason = $reasonPhrase;
        return $response;
    }
}
