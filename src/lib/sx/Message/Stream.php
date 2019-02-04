<?php
namespace Sx\Message;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{

    protected $resource;

    private $stats;

    private $metadata;

    public function __toString()
    {
        if (! $this->resource) {
            return '';
        }
        try {
            return $this->getContents() ?: '';
        } catch (\RuntimeException $e) {
            return '';
        }
    }

    public function close()
    {
        if ($this->resource) {
            fclose($this->resource);
        }
    }

    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }

    public function getSize()
    {
        if ($this->stats === null && $this->resource) {
            $this->stats = fstat($this->resource);
        }
        return $this->stats['size'] ?? null;
    }

    public function tell()
    {
        $position = $this->resource ? ftell($this->resource) : false;
        if ($position === false) {
            throw new \RuntimeException('unable to tell stream ' . $this->getMetadata('uri'));
        }
        return $position;
    }

    public function eof()
    {
        return $this->resource ? feof($this->resource) : true;
    }

    public function isSeekable()
    {
        return (bool) $this->getMetadata('seekable');
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        if (! $this->resource || fseek($this->resource, $offset, $whence) < 0) {
            throw new \RuntimeException('unable to seek in stream ' . $this->getMetadata('uri'));
        }
    }

    public function rewind()
    {
        if (! $this->resource) {
            throw new \RuntimeException('unable to rewind stream ' . $this->getMetadata('uri'));
        }
        if (! $this->isSeekable()) {
            throw new \RuntimeException(sprintf('stream %s is not seekable', $this->getMetadata('uri')));
        }
        if (! rewind($this->resource)) {
            throw new \RuntimeException('unable to rewind stream ' . $this->getMetadata('uri'));
        }
    }

    public function isWritable()
    {
        return strpos($this->getMetadata('mode'), 'w') !== false;
    }

    public function write($string)
    {
        if (! $this->resource) {
            return 0;
        }
        $bytes = fwrite($this->resource, $string);
        if ($bytes === false) {
            throw new \RuntimeException('unable to write to stream ' . $this->getMetadata('uri'));
        }
        return $bytes;
    }

    public function isReadable()
    {
        return strpos($this->getMetadata('mode'), 'r') !== false;
    }

    public function read($length)
    {
        $result = $this->resource ? fread($this->resource, $length) : false;
        if ($result === false) {
            throw new \RuntimeException('unable to read stream ' . $this->getMetadata('uri'));
        }
        return $result;
    }

    public function getContents()
    {
        $content = $this->resource ? stream_get_contents($this->resource) : false;
        if ($content === false) {
            throw new \RuntimeException('unable to get contents of stream ' . $this->getMetadata('uri'));
        }
        return $content;
    }

    public function getMetadata($key = null)
    {
        if ($this->metadata === null) {
            $this->metadata = stream_get_meta_data($this->resource);
        }
        if ($key) {
            return $this->metadata[$key] ?? null;
        }
        return $this->metadata;
    }
}
