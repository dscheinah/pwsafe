<?php
namespace Sx\Message;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Implements the functionality of all requests send to the server to be handled by middleware.
 *
 * @package Sx\Message
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * Hold all given server parameters including data not set by the client directly.
     *
     * @var array
     */
    protected $serverParams = [];

    /**
     * All cookies sent by the client.
     *
     * @var array
     */
    protected $cookieParams = [];

    /**
     * All query (GET) parameters.
     *
     * @var array
     */
    protected $queryParams = [];

    /**
     * All uploaded files.
     *
     * @var UploadedFileInterface[]
     */
    protected $uploads = [];

    /**
     * The parsed request body according to the content type of the request.
     *
     * @var mixed
     */
    protected $parsedBody;

    /**
     * Contains the combined GET and POST parameters added by custom attributes from middleware.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Creates a new server request with fixed server params.
     *
     * @param array $serverParams
     */
    public function __construct(array $serverParams = [])
    {
        $this->serverParams = $serverParams;
    }

    /**
     * Returns the server params given to the constructor.
     *
     * @return array
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * Returns all cookies.
     *
     * @return array
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * Sets new cookies on a new server request instance.
     *
     * @param array $cookies
     *
     * @return ServerRequestInterface|ServerRequest
     */
    public function withCookieParams(array $cookies)
    {
        $request = clone $this;
        $request->cookieParams = $cookies;
        return $request;
    }

    /**
     * Get all query (GET) parameters.
     *
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Sets new query parameters on a new server request instance. Attributes will be kept unchanged.
     *
     * @param array $query
     *
     * @return ServerRequestInterface|ServerRequest
     */
    public function withQueryParams(array $query)
    {
        $request = clone $this;
        $request->queryParams = $query;
        return $request;
    }

    /**
     * Returns all uploaded files.
     *
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->uploads;
    }

    /**
     * Sets new uploaded files on a new server request instance.
     *
     * @param array $uploadedFiles
     *
     * @return ServerRequestInterface|ServerRequest
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $request = clone $this;
        $request->uploads = $uploadedFiles;
        return $request;
    }

    /**
     * Returns the parsed body.
     *
     * @return array|object|null
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * Sets a new parsed body on a new server request instance. Attributes will be kept unchanged.
     *
     * @param array|object $data
     *
     * @return ServerRequestInterface|ServerRequest
     */
    public function withParsedBody($data)
    {
        $request = clone $this;
        $request->parsedBody = $data;
        return $request;
    }

    /**
     * Returns all attributes set for the request.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Returns the value for exactly one attribute. If the attribute was not set the default is returned.
     * The default is also return if the set attribute is falsify.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function getAttribute($name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Sets a new attribute on a new server request instance.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return ServerRequestInterface|ServerRequest
     */
    public function withAttribute($name, $value)
    {
        $request = clone $this;
        $request->attributes[$name] = $value;
        return $request;
    }

    /**
     * Removes an attribute from a new server request instance.
     *
     * @param string $name
     *
     * @return ServerRequestInterface|ServerRequest
     */
    public function withoutAttribute($name)
    {
        $request = clone $this;
        unset($request->attributes[$name]);
        return $request;
    }
}
