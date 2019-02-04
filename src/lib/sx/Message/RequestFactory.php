<?php
namespace Sx\Message;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UriFactoryInterface;

class RequestFactory extends Request implements RequestFactoryInterface
{

    protected $uriFactory;

    public function __construct(UriFactoryInterface $uriFactory)
    {
        $this->uriFactory = $uriFactory;
    }

    public function createRequest(string $method, $uri): RequestInterface
    {
        $request = new Request();

        if (! $uri instanceof UriInterface) {
            $uri = $this->uriFactory->createUri($uri);
        }
        $request->method = strtolower($method);
        $request->uri = $uri;
        $request->target = $uri->getPath();

        $headers = $mapper = [];
        foreach ($serverParams as $key => $value) {
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = substr($key, 5);
                $headers[$name] = $value;
                $mapper[str_replace('_', '-', strtolower($name))] = $name;
            }
        }
        $request->headers = $headers;
        $request->mapper = $mapper;

        return $request;
    }
}
