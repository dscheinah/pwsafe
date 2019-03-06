<?php
namespace Sx\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Implements the functionality of all requests.
 *
 * @package Sx\Message
 */
class Request extends Message implements RequestInterface
{
    /**
     * The target of the request.
     *
     * @var string
     */
    protected $target = '';

    /**
     * The method of the request.
     *
     * @var string
     */
    protected $method = '';

    /**
     * The URI of the request.
     *
     * @var UriInterface
     */
    protected $uri;

    /**
     * Returns the current target.
     *
     * @return string
     */
    public function getRequestTarget(): string
    {
        return $this->target;
    }

    /**
     * Sets the target on a new request instance.
     *
     * @param string $requestTarget
     *
     * @return RequestInterface|Request
     */
    public function withRequestTarget($requestTarget)
    {
        $request = clone $this;
        $request->target = (string) $requestTarget;
        return $request;
    }

    /**
     * Returns the method in lower case.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Sets the method on a new request instance. It will always be converted to lower case.
     *
     * @param string $method
     *
     * @return RequestInterface|Request
     */
    public function withMethod($method)
    {
        $request = clone $this;
        $request->method = strtolower($method);
        return $request;
    }

    /**
     * Returns the URI of the request.
     *
     * @return UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Sets the URI on a new request instance. The host header will used from the URIs host if preserveHost ist false.
     *
     * @param UriInterface $uri
     * @param bool         $preserveHost
     *
     * @return RequestInterface|Request
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $request = null;
        $host = $uri->getHost();
        if (!$preserveHost) {
            // If the host shall not be preserved it is replaced from the URI if available.
            if ($host) {
                $request = $this->withHeader(self::HEADER_HOST, $host);
            }
        } elseif ($host && !$this->getHeader(self::HEADER_HOST)) {
            // If the request does not have a host header the URIs host will also be applied.
            $request = $this->withHeader(self::HEADER_HOST, $host);
        }
        // Since the withHeader function above already cloned the request do it only if needed.
        if (!$request) {
            $request = clone $this;
        }
        $request->uri = $uri;
        return $request;
    }
}
