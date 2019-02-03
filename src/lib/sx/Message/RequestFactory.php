<?php
namespace Sx\Message;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UriFactoryInterface;

class RequestFactory implements RequestFactoryInterface
{

    protected $uri;

    public function __construct(UriFactoryInterface $uri = null)
    {
        $this->uri = $uri ?: new UriFactory();
    }

    public function createRequest(string $method, $uri): RequestInterface
    {
        $request = new Request();
        return $this->populateRequest($request, $method, $uri);
    }

    protected function populateRequest(RequestInterface $request, string $method, $uri): RequestInterface
    {
        if (! $uri instanceof UriInterface) {
            $uri = $this->uri->createUri($uri);
        }
        $request = $request->withMethod(strtolower($method));
        $request = $request->withUri($uri);
        $request = $request->withRequestTarget($uri->getPath());
        return $request;
    }
}
