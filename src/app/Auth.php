<?php
namespace App;

use Sx\Data\SessionException;
use Sx\Message\Response\HelperInterface;
use Sx\Server\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Data\SessionInterface;
use Sx\Server\MiddlewareHandlerException;
use App\Action\Login;

/**
 * This handler combines multiple roles of a complex application:
 * - early session handling to release the lock timely
 * - authentication and access checking
 * - public routes without authentication (therefore this is a router)
 *
 * @package App
 */
class Auth extends Router
{
    /**
     * The session wrapper to start, authentication check and end.
     *
     * @var SessionInterface
     */
    private $session;

    /**
     * Helper to create the not authorized response.
     *
     * @var HelperInterface
     */
    private $helper;

    /**
     * Creates the handler with session and response helper to create the not authorized response.
     *
     * @param MiddlewareHandlerInterface $handler
     * @param SessionInterface           $session
     * @param HelperInterface            $helper
     */
    public function __construct(MiddlewareHandlerInterface $handler, SessionInterface $session, HelperInterface $helper)
    {
        parent::__construct($handler);
        $this->session = $session;
        $this->helper = $helper;
    }

    /**
     * Starts and ends the session around the dispatch of the registered route actions.
     * Before calling the next handling session authentication ist checked. If not a AuthException is thrown.
     * It is also required to provide the encryption key as a request param for all non public actions.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws SessionException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // This is provided in the response of a successful login and must be appended to each other request.
        // The JS codes uses the header to not send the key by GET, which would make it visible in the logs.
        $key = $request->getAttribute('key', $request->getHeaderLine('X-KEY'));
        $this->session->start();
        try {
            // Do public dispatch as the parent router does. The exception is thrown if no action handled the request.
            $uri = $request->getUri();
            $routeHandler = $this->getHandler($request->getMethod(), $uri->getPath());
            if ($routeHandler) {
                return $routeHandler->handle($request);
            }
        } catch (MiddlewareHandlerException $e) {
            // Require the user ID provided by the login action. Also the frontend needs to send the encryption key.
            if (!$key || !$this->session->has(Login::class)) {
                return $this->helper->create(403);
            }
        } finally {
            // This releases the lock and closes the session. By design this happens as early as possible.
            // All next handlers should not use any session start again to prevent long locks.
            $this->session->end();
        }
        return $handler->handle(
            $request
                // Since the session should not be used in next handlers, provide the user ID as an attribute.
                ->withAttribute(Login::class, $this->session->get(Login::class))
                // When transferred as a header, it must still be accessible as an attribute for the actions.
                ->withAttribute('key', $key)
        );
    }
}
