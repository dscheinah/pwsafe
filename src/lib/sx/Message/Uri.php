<?php
namespace Sx\Message;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{

    const SCHEME_HTTP = 'http';

    const SCHEME_HTTPS = 'https';

    const SCHEME_FTP = 'ftp';

    protected $scheme = '';

    protected $user = '';

    protected $password = '';

    protected $host = '';

    protected $port = 0;

    protected $portMapping = [
        self::SCHEME_HTTP => 80,
        self::SCHEME_HTTPS => 443,
        self::SCHEME_FTP => 21
    ];

    protected $path = '';

    protected $query = '';

    protected $fragment = '';

    public function __toString()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();
        if ($scheme) {
            $scheme .= ':';
        }
        if ($authority) {
            $authority = '//' . $authority;
        }
        if (strpos($path, '/') === 0 && ! $authority) {
            $path = '/' . ltrim($path, '/');
        } elseif ($authority) {
            $path = '/' . $path;
        }
        if ($query) {
            $query = '?' . $query;
        }
        if ($fragment) {
            $fragment = '#' . $fragment;
        }
        return $scheme . $authority . $path . $query . $fragment;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getAuthority()
    {
        $host = $this->getHost();
        $user = $this->getUserInfo();
        $port = $this->getPort();
        if ($user) {
            $user .= '@';
        }
        if ($port) {
            $port = ':' . $port;
        }
        return $user . $host . $port;
    }

    public function getUserInfo()
    {
        $user = $this->user;
        if ($this->password) {
            $user .= ':' . $this->password;
        }
        return $user;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        $scheme = $this->getScheme();
        if (! $this->port || (isset($this->portMapping[$scheme]) && $this->port == $this->portMapping[$scheme])) {
            return null;
        }
        return $this->port;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return trim($this->query, '?');
    }

    public function getFragment()
    {
        return trim($this->fragment, '#');
    }

    public function withScheme($scheme)
    {
        $uri = clone $this;
        $uri->scheme = strtolower($scheme);
        return $uri;
    }

    public function withUserInfo($user, $password = null)
    {
        $uri = clone $this;
        $uri->user = $user;
        $uri->password = $password;
        return $uri;
    }

    public function withHost($host)
    {
        $uri = clone $this;
        $uri->host = $host;
        return $uri;
    }

    public function withPort($port)
    {
        $uri = clone $this;
        $uri->port = $port;
        return $uri;
    }

    public function withPath($path)
    {
        $uri = clone $this;
        $uri->path = $path;
        return $uri;
    }

    public function withQuery($query)
    {
        $uri = clone $this;
        $uri->query = $query;
        return $uri;
    }

    public function withFragment($fragment)
    {
        $uri = clone $this;
        $uri->fragment = $fragment;
        return $uri;
    }
}
