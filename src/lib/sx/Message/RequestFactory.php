<?php
namespace Sx\Message;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * A PSR compatible factory to create requests. It is not meant to be used as a factory for the Injector.
 * This class extends the Request to be able to set the protected properties without using separate setters or clones.
 *
 * @package Sx\Message
 */
class RequestFactory extends Request implements RequestFactoryInterface
{
    /**
     * The factory to create an URI.
     *
     * @var UriFactoryInterface
     */
    protected $uriFactory;

    /**
     * Creates a new request factory with the factory to create the URI from a string.
     *
     * @param UriFactoryInterface $uriFactory
     */
    public function __construct(UriFactoryInterface $uriFactory)
    {
        $this->uriFactory = $uriFactory;
    }

    /**
     * Creates a new Request with the given method and URI.
     *
     * @param string              $method
     * @param UriInterface|string $uri
     *
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        $request = new Request();
        // If the URI is given as a string it must be created using the factory.
        if (!$uri instanceof UriInterface) {
            $uri = $this->uriFactory->createUri($uri);
        }
        // Access the protected properties to avoid implementing setters or cloning by calling with...
        // This works as the factory extends the Request just for this reason.
        $request->method = strtolower($method);
        $request->uri = $uri;
        $request->target = $uri->getPath();
        return $request;
    }
}
