<?php
namespace Sx\Message;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * A PSR compatible factory to create URIs. It is not meant to be used as a factory for the Injector.
 * This class extends the Uri to be able to set the protected properties without using separate setters or clones.
 *
 * @package Sx\Message
 */
class UriFactory extends Uri implements UriFactoryInterface
{
    /**
     * Creates a new URI from string.
     *
     * @param string $uri
     *
     * @return UriInterface
     */
    public function createUri(string $uri = ''): UriInterface
    {
        $instance = new Uri();
        // Parse the string using the PHP built-in. It matches the internal naming of the Uri instance.
        foreach (parse_url($uri) as $key => $value) {
            // If for some reason parse_url returns more parts than available.
            if (isset($instance->$key)) {
                // Access the protected properties to avoid implementing setters or cloning by calling with...
                // This works as the factory extends the Response just for this reason.
                $instance->$key = $value;
            }
        }
        return $instance;
    }
}
