<?php
namespace App;

use App\Model\UserRepo;
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
     * The repository to load the current user for checking its role.
     *
     * @var UserRepo
     */
    private $userRepo;

    /**
     * Creates the handler with session and response helper to create the not authorized response.
     *
     * @param MiddlewareHandlerInterface $handler
     * @param SessionInterface           $session
     * @param HelperInterface            $helper
     * @param UserRepo                   $userRepo
     */
    public function __construct(
        MiddlewareHandlerInterface $handler,
        SessionInterface $session,
        HelperInterface $helper,
        UserRepo $userRepo
    ) {
        parent::__construct($handler);
        $this->session = $session;
        $this->helper = $helper;
        $this->userRepo = $userRepo;
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
        $path = $request->getUri()->getPath();
        // This is provided in the response of a successful login and must be appended to each other request.
        // The JS codes uses the header to not send the key by GET, which would make it visible in the logs.
        $key = $request->getAttribute('key', $request->getHeaderLine('X-KEY'));
        $this->session->start();
        try {
            // Do public dispatch as the parent router does. The exception is thrown if no action handled the request.
            return $this->getHandler($request->getMethod(), $path)->handle($request);
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
        $userId = $this->session->get(Login::class);
        // All user management routes can only be accessed by administrators.
        if (stripos($path, 'user') !== false && !$this->userRepo->isAdmin($userId)) {
            return $this->helper->create(403);
        }
        return $handler->handle(
            $request
                // Since the session should not be used in next handlers, provide the user ID as an attribute.
                ->withAttribute(Login::class, $userId)
                // When transferred as a header, it must still be accessible as an attribute for the actions.
                ->withAttribute('key', $key)
        );
    }
}
