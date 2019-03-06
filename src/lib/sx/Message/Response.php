<?php
namespace Sx\Message;

use Psr\Http\Message\ResponseInterface;

/**
 * Implements the functionality of all responses.
 *
 * @package Sx\Message
 */
class Response extends Message implements ResponseInterface
{
    /**
     * The status code of the response.
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * The status reason. There are currently no implemented defaults.
     *
     * @var string
     */
    protected $statusReason = '';

    /**
     * Returns the status code of the response.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Sets the status code on a new response instance.
     * The reason phrase is not mapped but kept empty if unset. This should be done by a helper or factory if needed.
     *
     * @param int    $code
     * @param string $reasonPhrase
     *
     * @return ResponseInterface|Response
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $response = clone $this;
        $response->statusCode = $code;
        $response->statusReason = $reasonPhrase;
        return $response;
    }

    /**
     * Returns the reason phrase. In all current implementations this is empty and therefore unused.
     *
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return $this->statusReason;
    }
}
