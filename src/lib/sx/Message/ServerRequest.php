<?php
namespace Sx\Message;

use Psr\Http\Message\ServerRequestInterface;

class ServerRequest extends Request implements ServerRequestInterface
{

    protected $serverParams = [];

    protected $cookieParams = [];

    protected $queryParams = [];

    protected $uploads = [];

    protected $parsedBody;

    protected $attributes = [];

    public function __construct(array $serverParams = [])
    {
        $this->serverParams = $serverParams;
    }

    public function getServerParams()
    {
        return $this->serverParams;
    }

    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    public function withCookieParams(array $cookies)
    {
        $request = clone $this;
        $request->cookieParams = $cookies;
        return $request;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function withQueryParams(array $query)
    {
        $request = clone $this;
        $request->queryParams = $query;
        return $request;
    }

    public function getUploadedFiles()
    {
        return $this->uploads;
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        $request = clone $this;
        $request->uploads = $uploadedFiles;
        return $request;
    }

    public function getParsedBody()
    {
        return $this->pasedBody;
    }

    public function withParsedBody($data)
    {
        $request = clone $this;
        $request->parsedBody = $data;
        return $request;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    public function withAttribute($name, $value)
    {
        $request = clone $this;
        $request->attributes[$name] = $value;
        return $request;
    }

    public function withoutAttribute($name)
    {
        $request = clone $this;
        unset($request->attributes[$name]);
        return $request;
    }
}
