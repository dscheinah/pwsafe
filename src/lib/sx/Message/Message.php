<?php
namespace Sx\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class Message implements MessageInterface
{

    const HEADER_HOST = 'HOST';

    protected $version = 0.0;

    protected $headers = [];

    protected $mapper = [];

    protected $body;

    public function getProtocolVersion()
    {
        return $this->version;
    }

    public function withProtocolVersion($version)
    {
        $message = clone $this;
        $message->version = $version;
        return $message;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        $name = strtolower($name);
        return isset($this->mapper[$name]) && isset($this->headers[$this->mapper[$name]]);
    }

    public function getHeader($name)
    {
        $name = strtolower($name);
        if (! $this->hasHeader($name)) {
            return [];
        }
        return $this->headers[$this->mapper[$name]];
    }

    public function getHeaderLine($name)
    {
        return implode(',', $this->getHeader($name));
    }

    public function withHeader($name, $value)
    {
        if (! is_array($value)) {
            $value = [
                $value
            ];
        }
        $message = null;
        $lowerName = strtolower($name);
        if (isset($this->mapper[$lowerName])) {
            $message = $this->withoutHeader($lowerName);
        }
        if (! $message) {
            $message = clone $this;
        }
        $message->mapper[$lowerName] = $name;
        $message->headers[$name] = $value;
        return $message;
    }

    public function withAddedHeader($name, $value)
    {
        $header = $this->getHeader($name);
        if (! is_array($value)) {
            $value = [
                $value
            ];
        }
        foreach ($value as $current) {
            $header[] = $current;
        }
        $message = $this->withoutHeader($name);
        $message->mapper[strtolower($name)] = $name;
        $message->headers[$name] = $header;
        return $message;
    }

    public function withoutHeader($name)
    {
        $message = clone $this;
        unset($message->mapper[strtolower($name)]);
        unset($message->headers[$name]);
        return $message;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $message = clone $this;
        $message->body = $body;
        return $message;
    }
}
