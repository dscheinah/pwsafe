<?php
namespace Sx\Message;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class UriFactory extends Uri implements UriFactoryInterface
{

    public function createUri(string $uri = ''): UriInterface
    {
        $instance = new Uri();
        $parts = parse_url($uri);
        if (isset($parts['pass'])) {
            $parts['password'] = $parts['pass'];
            unset($parts['pass']);
        }
        foreach ($parts as $key => $value) {
            if (isset($instance->$key)) {
                $instance->$key = $value;
            }
        }
        return $instance;
    }
}
