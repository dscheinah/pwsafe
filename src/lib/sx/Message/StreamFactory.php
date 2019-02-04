<?php
namespace Sx\Message;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class StreamFactory extends Stream implements StreamFactoryInterface
{

    public function createStream(string $content = ''): StreamInterface
    {
        $resource = fopen('php://memory', 'r+');
        $stream = $this->createStreamFromResource($resource);
        if ($content) {
            $stream->write($content);
            $stream->rewind();
        }
        return $stream;
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        $file = fopen($filename, $mode);
        if (! $file) {
            $file = fopen('php://temp', $mode);
        }
        return $this->createStreamFromResource($file);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        $stream = new Stream();
        $stream->resource = $resource;
        return $stream;
    }
}
