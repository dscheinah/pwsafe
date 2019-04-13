<?php
namespace App\Action;

use App\MiddlewareAbstract;
use App\Model\GroupRepo;
use App\Model\RepoException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\HelperInterface;

/**
 * The action to load all data of a group for the edit view.
 * It is also used as a base class for all other group actions.
 *
 * @package App\Action
 */
class Group extends MiddlewareAbstract
{
    /**
     * The domain repository to handle all queries as a bridge to the database.
     *
     * @var GroupRepo
     */
    protected $repo;

    /**
     * Creates the action with helper and domain repository.
     *
     * @param HelperInterface $helper
     * @param GroupRepo       $repo
     */
    public function __construct(HelperInterface $helper, GroupRepo $repo)
    {
        parent::__construct($helper);
        $this->repo = $repo;
    }

    /**
     * Loads the data for the given group and returns it to the client.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $group = $this->repo->getGroup($request->getAttribute('id'));
            return $this->helper->create(200, $group);
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
    }
}
