<?php
namespace App\Action;

use App\MiddlewareAbstract;
use App\Model\RepoException;
use App\Model\UserRepo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\HelperInterface;

/**
 * Action to handle the save of the clients profile (including login data).
 *
 * @package App\Action
 */
class ProfileSave extends MiddlewareAbstract
{
    /**
     * This is the bridge between domain and database.
     *
     * @var UserRepo
     */
    protected $repo;

    /**
     * Creates the profile action with domain repository.
     *
     * @param HelperInterface $helper
     * @param UserRepo        $repo
     */
    public function __construct(HelperInterface $helper, UserRepo $repo)
    {
        parent::__construct($helper);
        $this->repo = $repo;
    }

    /**
     * Tries to save the given data to the logged in user. The currently valid clients password must be provided.
     * The response contains the afterwards valid user data to be updated by the client interface.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->getAttribute('user')) {
            return $this->helper->create(422, ['message' => 'Der Benutzer muss gefüllt werden.']);
        }
        // This contains checks for valid password and requires, if changed, both new password fields to match.
        try {
            $user = $this->repo->saveUser($request->getAttribute(Login::class), $request->getAttributes());
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
        if (!$user) {
            // Forward failures to the next handler.
            return $handler->handle($request);
        }
        // ID and password hash are not required for the client and therefore should not be sent.
        unset($user['id'], $user['password']);
        return $this->helper->create(200, $user);
    }
}
