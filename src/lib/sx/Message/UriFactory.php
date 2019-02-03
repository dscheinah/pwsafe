<?php
namespace Sx\Message;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class UriFactory implements UriFactoryInterface
{

    public function createUri(string $uri = ''): UriInterface
    {
        $uri = new Uri();
        $parts = parse_url($uri);
        if ($parts) {
            if (isset($parts['scheme'])) {
                $uri = $uri->withScheme($parts['scheme']);
            }
            if (isset($parts['user']) || isset($parts['pass'])) {
                $uri = $uri->withUserInfo($parts['user'] ?? '', $parts['pass'] ?? '');
            }
            if (isset($parts['host'])) {
                $uri = $uri->withHost($parts['host']);
            }
            if (isset($parts['port'])) {
                $uri = $uri->withPort($parts['port']);
            }
            if (isset($parts['path'])) {
                $uri = $uri->withPath($parts['path']);
            }
            if (isset($parts['query'])) {
                $uri = $uri->withQuery($parts['query']);
            }
            if (isset($parts['fragment'])) {
                $uri = $uri->withFragment($parts['fragment']);
            }
        }
        return $uri;
    }
}
