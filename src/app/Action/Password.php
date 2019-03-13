<?php
namespace App\Action;

use App\MiddlewareAbstract;
use App\Model\RepoException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Sx\Message\Response\HelperInterface;
use App\Model\PasswordRepo;

/**
 * Action to load all data for one password entry. It is also used as a base class for all other password actions.
 *
 * @package App\Action
 */
class Password extends MiddlewareAbstract
{
    /**
     * This is the bridge between domain and database.
     *
     * @var PasswordRepo
     */
    protected $repo;

    /**
     * Creates the action with domain repository.
     *
     * @param HelperInterface $helper
     * @param PasswordRepo    $repo
     */
    public function __construct(HelperInterface $helper, PasswordRepo $repo)
    {
        parent::__construct($helper);
        $this->repo = $repo;
    }

    /**
     * Loads a password and returns the data to the client.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Set user ID and key from request.
        $this->prepareRepo($request);
        // Load a password entry by id.
        try {
            $password = $this->repo->getPassword($request->getAttribute('id'));
            // Needed for new passwords to be rendered in the list.
            $password['term'] = '';
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
        return $this->helper->create(200, $password);
    }

    /**
     * Sets the required parameters (user ID and key) to the repository. This method is also used by derived classes.
     * There is no extra check since the Auth handler already checked and the repository will also check before sending
     * request to the storage.
     *
     * @param ServerRequestInterface $request
     */
    protected function prepareRepo(ServerRequestInterface $request): void
    {
        // This prevents the client to load data not assigned to the logged in user ID.
        $this->repo->setUser($request->getAttribute(Login::class));
        // Needed to encrypt the stored data. A separate key is used to allow password changes without re-encryption
        // of all passwords. The key itself is stored encrypted by the current password.
        $this->repo->setKey($request->getAttribute('key'));
    }
}
