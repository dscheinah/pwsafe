<?php
namespace Sx\Message;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;

class ServerRequestFactory extends ServerRequest implements ServerRequestFactoryInterface
{

    protected $uriFactory;

    public function __construct(UriFactoryInterface $uriFactory)
    {
        $this->uriFactory = $uriFactory;
    }

    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $request = new ServerRequest($serverParams);

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

        $request->attributes = $_GET + $_POST;
        $request->cookieParams = $_COOKIE;
        $request->queryParams = $_GET;
        $request->parsedBody = $_POST;

        return $request;
    }
}
