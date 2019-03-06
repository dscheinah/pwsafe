<?php
namespace Sx\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * The PSR message implementation used by Requests and Responses.
 *
 * @package Sx\Message
 */
class Message implements MessageInterface
{
    // This header is used in Request for withUri.
    public const HEADER_HOST = 'host';

    /**
     * The protocol version of the message. If implemented a reasonable default should be set.
     *
     * @var string
     */
    protected $version = '0';

    /**
     * The headers of the message collected as an array of values for each header.
     *
     * @var string[][]
     */
    protected $headers = [];

    /**
     * The mapping of lowercase header names to the original names present in headers.
     *
     * @var string[]
     */
    protected $mapper = [];

    /**
     * The message body as a stream.
     *
     * @var StreamInterface
     */
    protected $body;

    /**
     * Returns the current protocol version. This will only return 0 in all current implementations.
     *
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->version;
    }

    /**
     * Creates a new message instance with the set version.
     *
     * @param string $version
     *
     * @return MessageInterface|Message
     */
    public function withProtocolVersion($version)
    {
        $message = clone $this;
        $message->version = (string) $version;
        return $message;
    }

    /**
     * Returns all headers as an array of an array of values. The first level contains the names as keys.
     *
     * @return string[][]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Checks if the header with the given name has been set.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasHeader($name): bool
    {
        // Since headers are case insensitive the mapper is used to check the insensitive name.
        $name = strtolower($name);
        return isset($this->mapper[$name], $this->headers[$this->mapper[$name]]);
    }

    /**
     * Returns the value for the given name as an array of values.
     *
     * @param string $name
     *
     * @return string[]
     */
    public function getHeader($name) : array
    {
        // Use the lower name to represent the insensitive header.
        $name = strtolower($name);
        if (!$this->hasHeader($name)) {
            return [];
        }
        // With the previous check of hasHeader the indizes must be accessible.
        return $this->headers[$this->mapper[$name]];
    }

    /**
     * Returns the header line imploded from the values of the given header.
     *
     * @param string $name
     *
     * @return string
     */
    public function getHeaderLine($name): string
    {
        return implode(',', $this->getHeader($name));
    }

    /**
     * Sets a new header on a new message instance. The name will be stored as is but accessible as insensitive key.
     *
     * @param string          $name
     * @param string|string[] $value
     *
     * @return MessageInterface|Message
     */
    public function withHeader($name, $value)
    {
        // Allow a string to be set, but ensure to store values as an array.
        if (!\is_array($value)) {
            $value = [
                $value,
            ];
        }
        // Use the lower variant to store a the mapping to the original name.
        // All other header functions will do the same when searching for a header given by name.
        $lowerName = strtolower($name);
        // If the header was already set it needs to be unset with it's original name. Just replacing the name in
        // the headers array would not help, if the names cases do not match.
        if (isset($this->mapper[$lowerName])) {
            $message = $this->withoutHeader($lowerName);
        } else {
            // The call to withoutHeader already cloned the message. So only do it here.
            $message = clone $this;
        }
        // Set the header and the insensitive key.
        $message->mapper[$lowerName] = $name;
        $message->headers[$name] = $value;
        return $message;
    }

    /**
     * Appends a value to the header on a new message instance.
     *
     * @param string          $name
     * @param string|string[] $value
     *
     * @return MessageInterface|Message
     */
    public function withAddedHeader($name, $value)
    {
        // Get the already set header.
        $header = $this->getHeader($name);
        // Append all new values to the header.
        if (!\is_array($value)) {
            $value = [
                $value,
            ];
        }
        foreach ($value as $current) {
            $header[] = $current;
        }
        // Replace the header. Unset first to not duplicate the entry if called with differently cased names.
        $message = $this->withoutHeader($name);
        $message->mapper[strtolower($name)] = $name;
        $message->headers[$name] = $header;
        return $message;
    }

    /**
     * Removes a header from a new message instance.
     *
     * @param string $name
     *
     * @return MessageInterface|Message
     */
    public function withoutHeader($name)
    {
        $message = clone $this;
        unset($message->mapper[strtolower($name)], $message->headers[$name]);
        return $message;
    }

    /**
     * Returns the currently set body as a stream if it was set before.
     *
     * @return StreamInterface|null
     */
    public function getBody(): ?StreamInterface
    {
        return $this->body;
    }

    /**
     * Sets the body on a new message instance.
     *
     * @param StreamInterface $body
     *
     * @return MessageInterface|Message
     */
    public function withBody(StreamInterface $body)
    {
        $message = clone $this;
        $message->body = $body;
        return $message;
    }
}
