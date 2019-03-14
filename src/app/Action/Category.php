<?php
namespace App\Action;

use App\MiddlewareAbstract;
use App\Model\CategoryRepo;
use App\Model\RepoException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\HelperInterface;

/**
 * The action to load all data of category for the edit view.
 * It is also used as a base class for all other category actions.
 *
 * @package App\Action
 */
class Category extends MiddlewareAbstract
{
    /**
     * The domain repository to handle all queries as a bridge to the database.
     *
     * @var CategoryRepo
     */
    protected $repo;

    /**
     * Creates the action with helper and domain repository.
     *
     * @param HelperInterface $helper
     * @param CategoryRepo    $repo
     */
    public function __construct(HelperInterface $helper, CategoryRepo $repo)
    {
        parent::__construct($helper);
        $this->repo = $repo;
    }

    /**
     * Loads the data for the given category and returns it to the client.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->prepareRepo($request);
        // Load a category by id.
        try {
            $category = $this->repo->getCategory($request->getAttribute('id'));
            return $this->helper->create(200, $category);
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
    }

    /**
     * Sets the required use ID to the repository. This method is also used by derived classes.
     * There is no extra check since the Auth handler already checked and the repository will also check before sending
     * request to the storage.
     *
     * @param ServerRequestInterface $request
     */
    protected function prepareRepo(ServerRequestInterface $request): void
    {
        // This prevents the client to load data not assigned to the logged in user ID.
        $this->repo->setUser($request->getAttribute(Login::class));
    }
}
