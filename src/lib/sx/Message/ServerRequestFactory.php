<?php
namespace Sx\Message;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * A PSR compatible factory to create responses. It is not meant to be used as a factory for the Injector.
 * This class extends the ServerRequest to be able to set the properties without using separate setters or clones.
 *
 * @package Sx\Message
 */
class ServerRequestFactory extends ServerRequest implements ServerRequestFactoryInterface
{
    /**
     * The factory to create an URI from string.
     *
     * @var UriFactoryInterface
     */
    protected $uriFactory;

    /**
     * Creates the factory with an URI factory to creates URIs from string,
     *
     * @param UriFactoryInterface $uriFactory
     */
    public function __construct(UriFactoryInterface $uriFactory)
    {
        $this->uriFactory = $uriFactory;
    }

    /**
     * Creates a new ServerRequest with the given method and URI. The serverParams will be given as server params
     * to the request and are used to set headers.
     * This factory currently does not support file uploads and only parses the body by using the POST super global.
     *
     * @param string              $method
     * @param UriInterface|string $uri
     * @param array               $serverParams
     *
     * @return ServerRequestInterface
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $request = new ServerRequest($serverParams);
        // If the URI is provided as a string it must be converted using the factory.
        if (!$uri instanceof UriInterface) {
            $uri = $this->uriFactory->createUri($uri);
        }
        // Access the protected properties to avoid implementing setters or cloning by calling with...
        // This works as the factory extends the ServerRequest just for this reason.
        $request->method = strtolower($method);
        $request->uri = $uri;
        $request->target = $uri->getPath();
        // Set the headers from server params. The web server prepended http_ which needs to be removed.
        $headers = $mapper = [];
        foreach ($serverParams as $key => $value) {
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = substr($key, 5);
                $headers[$name] = \is_array($value) ? $value : [$value];
                // Convert the underscore from the web servers conversion to the canonical minus.
                // Also fill the insensitive mapper as no withHeader is used.
                $mapper[str_replace('_', '-', strtolower($name))] = $name;
            }
        }
        $request->headers = $headers;
        $request->mapper = $mapper;
        // "Parse" the attributes, params and body from the super globals.
        $request->attributes = $_GET + $_POST;
        $request->queryParams = $_GET;
        $request->parsedBody = $_POST;
        // Also use the cookies provided by PHP.
        $request->cookieParams = $_COOKIE;
        return $request;
    }
}
