<?php
namespace Sx\Message\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * A helper is a special kind of factory for responses. It is meant to be used in middleware to create responses.
 *
 * @package Sx\Message\Response
 */
interface HelperInterface
{
    /**
     * Must create a response with given status code. The response parameter is used as the response body.
     * The helper may decide how to decode which response types into the return value.
     *
     * @param int   $code
     * @param mixed $response
     *
     * @return ResponseInterface
     */
    public function create(int $code, $response): ResponseInterface;
}
