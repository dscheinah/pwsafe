<?php
namespace Sx\Message;

use Psr\Http\Message\UriInterface;

/**
 * This class provides all functionality for URIs used in the context of messages.
 *
 * @package Sx\Message
 */
class Uri implements UriInterface
{
    // The available schemes.
    public const SCHEME_HTTP = 'http';
    public const SCHEME_HTTPS = 'https';
    public const SCHEME_FTP = 'ftp';

    /**
     * The scheme part of the URL.
     *
     * @var string
     */
    protected $scheme = '';

    /**
     * The user of the provided login data.
     *
     * @var string
     */
    protected $user = '';

    /**
     * The password of the provided login data.
     *
     * @var string
     */
    protected $pass = '';

    /**
     * The host part of the URL.
     *
     * @var string
     */
    protected $host = '';

    /**
     * The used port. Zero is mapped to default according to the set scheme.
     *
     * @var int
     */
    protected $port = 0;

    /**
     * To omit the port if it matches the set schemes default this mapping is used.
     *
     * @var array
     */
    protected $portMapping = [
        self::SCHEME_HTTP => 80,
        self::SCHEME_HTTPS => 443,
        self::SCHEME_FTP => 21,
    ];

    /**
     * The path part of the URL.
     *
     * @var string
     */
    protected $path = '';

    /**
     * The query string of the URL.
     *
     * @var string
     */
    protected $query = '';

    /**
     * The fragment part of the URL.
     *
     * @var string
     */
    protected $fragment = '';

    /**
     * Combines all set parts of the URI to a ready to use string.
     *
     * @return string
     */
    public function __toString()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();
        // If a scheme is set a colon must follow.
        if ($scheme) {
            $scheme .= ':';
        }
        // The authority must be prefixed with a double slash in all cases.
        if ($authority) {
            $authority = '//' . $authority;
        }
        if (strpos($path, '/') === 0) {
            // The path must only start with one leading slash.
            $path = '/' . ltrim($path, '/');
        } elseif ($authority) {
            // If an authority is present the path must be separated with a slash.
            $path = '/' . $path;
        }
        // The query does not contain the leading question mark so it must be prepended.
        if ($query) {
            $query = '?' . $query;
        }
        // The fragment does not contain the leading hash sign so it must be prepended.
        if ($fragment) {
            $fragment = '#' . $fragment;
        }
        // Combine all prepared parts into one correct string.
        return $scheme . $authority . $path . $query . $fragment;
    }

    /**
     * Returns the scheme part of the URL.
     *
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Returns the combined authority from user, password, host and port.
     *
     * @return string
     */
    public function getAuthority(): string
    {
        $host = $this->getHost();
        $user = $this->getUserInfo();
        $port = $this->getPort();
        // User information must be separated with an at from the host part.
        if ($user) {
            $user .= '@';
        }
        // The port is indicated by a leading colon after the host part.
        if ($port) {
            $port = ':' . $port;
        }
        return $user . $host . $port;
    }

    /**
     * Returns the combined user information from user and password.
     *
     * @return string
     */
    public function getUserInfo(): string
    {
        $user = $this->user;
        if ($this->pass) {
            $user .= ':' . $this->pass;
        }
        return $user;
    }

    /**
     * Returns the host part of the URL.
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Returns the port if set and not default for the set scheme.
     *
     * @return int|null
     */
    public function getPort(): ?int
    {
        $scheme = $this->getScheme();
        if (!$this->port || (isset($this->portMapping[$scheme]) && $this->port === $this->portMapping[$scheme])) {
            return null;
        }
        return $this->port;
    }

    /**
     * Returns the path part of the URL.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns the query string of the URL without leading (or trailing) question mark.
     *
     * @return string
     */
    public function getQuery(): string
    {
        return trim($this->query, '?');
    }

    /**
     * Returns the fragment part of the URL without leading (or trailing) hash sign.
     *
     * @return string
     */
    public function getFragment(): string
    {
        return trim($this->fragment, '#');
    }

    /**
     * Sets the scheme on a new uri instance.
     *
     * @param string $scheme
     *
     * @return UriInterface|Uri
     */
    public function withScheme($scheme)
    {
        $uri = clone $this;
        $uri->scheme = strtolower($scheme);
        return $uri;
    }

    /**
     * Sets user and password on a new uri instance.
     *
     * @param string $user
     * @param string $password
     *
     * @return UriInterface|Uri
     */
    public function withUserInfo($user, $password = null)
    {
        $uri = clone $this;
        $uri->user = $user;
        $uri->pass = $password;
        return $uri;
    }

    /**
     * Sets the host on a new uri instance.
     *
     * @param string $host
     *
     * @return UriInterface|Uri
     */
    public function withHost($host)
    {
        $uri = clone $this;
        $uri->host = $host;
        return $uri;
    }

    /**
     * Sets the port on a new uri instance.
     *
     * @param int $port
     *
     * @return UriInterface|Uri
     */
    public function withPort($port)
    {
        $uri = clone $this;
        $uri->port = (int)$port;
        return $uri;
    }

    /**
     * Sets the path on a new uri instance.
     *
     * @param string $path
     *
     * @return UriInterface|Uri
     */
    public function withPath($path)
    {
        $uri = clone $this;
        $uri->path = $path;
        return $uri;
    }

    /**
     * Sets the query string on a new uri instance.
     *
     * @param string $query
     *
     * @return UriInterface|Uri
     */
    public function withQuery($query)
    {
        $uri = clone $this;
        $uri->query = $query;
        return $uri;
    }

    /**
     * Sets the fragment on a new uri instance.
     *
     * @param string $fragment
     *
     * @return UriInterface|Uri
     */
    public function withFragment($fragment)
    {
        $uri = clone $this;
        $uri->fragment = $fragment;
        return $uri;
    }
}
