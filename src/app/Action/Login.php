<?php
namespace App\Action;

use App\MiddlewareAbstract;
use App\Model\UserRepo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Data\SessionInterface;
use Sx\Message\Response\HelperInterface;

/**
 * This actions handles the login with provided user and password.
 *
 * @package App\Action
 */
class Login extends MiddlewareAbstract
{
    /**
     * The session is required to store the user ID of the logged in user.
     *
     * @var SessionInterface
     */
    protected $session;

    /**
     * This is the bridge between domain and database.
     *
     * @var UserRepo
     */
    protected $repo;

    /**
     * Creates the login action with session and domain repository.
     *
     * @param HelperInterface  $helper
     * @param SessionInterface $session
     * @param UserRepo         $repo
     */
    public function __construct(HelperInterface $helper, SessionInterface $session, UserRepo $repo)
    {
        parent::__construct($helper);
        $this->session = $session;
        $this->repo = $repo;
    }

    /**
     * Handles the login by checking user and password.
     * If valid the session will contain the user ID scoped with the class name of this action. Also the encryption key
     * is provided to be used by the client. Both are needed to pass authentication in the Auth handler.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws \App\Model\RepoException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $request->getAttribute('user') ?: '';
        $password = $request->getAttribute('password') ?: '';
        // First check the password of the given user. Both parameters need to be string (not null).
        if (!$this->repo->checkPassword($user, $password)) {
            return $this->helper->create(
                403,
                [
                    'user' => $user,
                    'message' => 'login data is invalid',
                ]
            );
        }
        // Get the complete user data if password check successful and store the user id.
        $user = $this->repo->getUser($user, $password);
        $this->session->set(__CLASS__, $user['id']);
        // ID and hashed password should not be sent to the client since not needed.
        unset($user['id'], $user['password']);
        return $this->helper->create(200, $user);
    }
}
