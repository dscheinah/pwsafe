<?php
namespace Sx\Message;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A PSR compatible factory to create streams. It is not meant to be used as a factory for the Injector.
 *
 * @package Sx\Message
 */
class StreamFactory implements StreamFactoryInterface
{
    /**
     * Creates a Stream from string.
     *
     * @param string $content
     *
     * @return StreamInterface
     */
    public function createStream(string $content = ''): StreamInterface
    {
        // Use a in memory file stream to abstract the string to a file resource usable by Stream.
        $resource = fopen('php://memory', 'rb+');
        $stream = $this->createStreamFromResource($resource);
        if ($content) {
            // Fill the Stream with content if given.
            $stream->write($content);
            $stream->rewind();
        }
        return $stream;
    }

    /**
     * Creates a Stream from file with given open mode.
     *
     * @param string $filename
     * @param string $mode
     *
     * @return StreamInterface
     */
    public function createStreamFromFile(string $filename, string $mode = 'rb'): StreamInterface
    {
        // Open the file but allow temporary files to always return an usable Stream.
        $file = fopen($filename, $mode);
        if (!$file) {
            $file = fopen('php://temp', $mode);
        }
        return $this->createStreamFromResource($file);
    }

    /**
     * Creates a Stream from an already created resource.
     *
     * @param resource $resource
     *
     * @return StreamInterface
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
