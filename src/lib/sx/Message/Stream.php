<?php
namespace Sx\Message;

use Psr\Http\Message\StreamInterface;

/**
 * A universal stream implementation to be used by messages.
 *
 * @package Sx\Message
 */
class Stream implements StreamInterface
{
    /**
     * The current underlying stream resource.
     *
     * @var resource
     */
    protected $resource;

    /**
     * The cache for the fstat call. Initialized with null to also cache empty results.
     *
     * @var array
     */
    private $stats;

    /**
     * The cache for the stream_get_meta_data call. Initialized with null to also cache empty results.
     *
     * @var array
     */
    private $metadata;

    /**
     * Creates a stream for the given resource.
     *
     * @param resource $resource
     */
    public function __construct($resource = null)
    {
        $this->resource = $resource;
    }

    /**
     * Returns the complete stream content as one (probably binary) string.
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->resource) {
            return '';
        }
        try {
            return $this->getContents();
        } catch (\RuntimeException $e) {
            // The __toString does not like exceptions.
            return '';
        }
    }

    /**
     * Closes the resource. This will make the Stream unusable.
     */
    public function close(): void
    {
        if ($this->resource) {
            fclose($this->resource);
        }
    }

    /**
     * Detaches the resource from the Stream. This makes the Stream unusable but returns the resource for outer usage.
     *
     * @return resource|null
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }

    /**
     * Returns the size of the Stream in bytes.
     *
     * @return int|null
     */
    public function getSize(): ?int
    {
        // Load the size from fstat but cache it to avoid unnecessary operation.
        if ($this->stats === null && $this->resource) {
            $this->stats = fstat($this->resource);
        }
        return $this->stats['size'] ? (int)$this->stats['size'] : null;
    }

    /**
     * Returns the current position inside the Stream.
     *
     * @return int
     * @throws \RuntimeException
     */
    public function tell(): int
    {
        $position = $this->resource ? ftell($this->resource) : false;
        if ($position === false) {
            throw new \RuntimeException('unable to tell stream ' . $this->getMetadata('uri'));
        }
        return $position;
    }

    /**
     * Moves the Streams position to the end.
     *
     * @return bool
     */
    public function eof(): bool
    {
        return $this->resource ? feof($this->resource) : true;
    }

    /**
     * Checks if the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable(): bool
    {
        return (bool)$this->getMetadata('seekable');
    }

    /**
     * Moves the position inside the Stream with fseek.
     *
     * @param int $offset
     * @param int $whence
     *
     * @throws \RuntimeException
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!$this->resource || fseek($this->resource, $offset, $whence) < 0) {
            throw new \RuntimeException('unable to seek in stream ' . $this->getMetadata('uri'));
        }
    }

    /**
     * Returns the position to the start of the Stream.
     *
     * @throws \RuntimeException
     */
    public function rewind(): void
    {
        if (!$this->resource) {
            throw new \RuntimeException('unable to rewind stream ' . $this->getMetadata('uri'));
        }
        if (!$this->isSeekable()) {
            throw new \RuntimeException(sprintf('stream %s is not seekable', $this->getMetadata('uri')));
        }
        if (!rewind($this->resource)) {
            throw new \RuntimeException('unable to rewind stream ' . $this->getMetadata('uri'));
        }
    }

    /**
     * Checks if the Stream is writable.
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        return strpos($this->getMetadata('mode'), 'w') !== false;
    }

    /**
     * Write data to the Stream as the current position.
     *
     * @param string $string
     *
     * @return int
     * @throws \RuntimeException
     */
    public function write($string): int
    {
        if (!$this->resource) {
            return 0;
        }
        $bytes = fwrite($this->resource, $string);
        if ($bytes === false) {
            throw new \RuntimeException('unable to write to stream ' . $this->getMetadata('uri'));
        }
        // Invalidate the size cache.
        $this->stats = null;
        return $bytes;
    }

    /**
     * Checks if the Stream is readable.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return strpos($this->getMetadata('mode'), 'r') !== false;
    }

    /**
     * Read length bytes data from the Stream.
     *
     * @param int $length
     *
     * @return string
     * @throws \RuntimeException
     */
    public function read($length): string
    {
        $result = $this->resource ? fread($this->resource, $length) : false;
        if ($result === false) {
            throw new \RuntimeException('unable to read stream ' . $this->getMetadata('uri'));
        }
        return $result;
    }

    /**
     * Returns the complete (possibly binary) content from the Stream.
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getContents(): string
    {
        $content = $this->resource ? stream_get_contents($this->resource) : '';
        if ($content === false) {
            throw new \RuntimeException('unable to get contents of stream ' . $this->getMetadata('uri'));
        }
        return $content;
    }

    /**
     * Loads all or selected meta data from the Stream. The available keys match stream_get_meta_data.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getMetadata($key = null)
    {
        // Load the meta data but cache it to prevent unnecessary calls to stream_get_meta_data.
        if ($this->metadata === null) {
            $this->metadata = stream_get_meta_data($this->resource);
        }
        // If a key is given return the value (or null) for the key.
        if ($key !== null) {
            return $this->metadata[$key] ?? null;
        }
        // Only return all meta data if no key is given.
        return $this->metadata;
    }
}
