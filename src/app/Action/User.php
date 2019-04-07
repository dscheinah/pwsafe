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
 * The action to load the data of one user for administration.
 *
 * @package App\Action
 */
class User extends MiddlewareAbstract
{
    /**
     * The domain repository to handle the user data.
     *
     * @var UserRepo
     */
    protected $repo;

    /**
     * Creates the action with the response helper and the domain repository.
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
     * Loads the data for the given user and returns it to the client.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $user = $this->repo->getUser($request->getAttribute('id'));
            return $this->helper->create(200, $user);
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
    }
}
