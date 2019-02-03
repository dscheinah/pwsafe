<?php
namespace Sx\Message;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestFactory extends RequestFactory implements ServerRequestFactoryInterface
{

    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $request = new ServerRequest($serverParams);
        $request = $this->populateRequest($request, $method, $uri);
        foreach ($serverParams as $key => $value) {
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = strtr(strtolower(substr($key, 5)), '_', '-');
                $request = $request->withAddedHeader($name, $value);
            }
        }
        foreach ($_GET + $_POST as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }
        $request = $request->withCookieParams($_COOKIE);
        $request = $request->withQueryParams($_GET);
        $request = $request->withParsedBody($_POST);
        return $request;
    }
}
