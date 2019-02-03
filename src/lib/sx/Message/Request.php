<?php
namespace Sx\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{

    protected $target = '';

    protected $method = '';

    protected $uri;

    public function getRequestTarget()
    {
        return $this->target;
    }

    public function withRequestTarget($requestTarget)
    {
        $request = clone $this;
        $request->target = $requestTarget;
        return $request;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        $request = clone $this;
        $request->method = strtolower($method);
        return $request;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $request = null;
        $host = $uri->getHost();
        if (! $preserveHost) {
            if ($host) {
                $request = $this->withHeader(self::HEADER_HOST, $host);
            }
        } else {
            if ($host && ! $this->getHeader(self::HEADER_HOST)) {
                $request = $this->withHeader(self::HEADER_HOST, $host);
            }
        }
        if (! $request) {
            $request = clone $this;
        }
        $request->uri = $uri;
        return $request;
    }
}
