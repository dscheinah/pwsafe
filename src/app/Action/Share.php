<?php
namespace App\Action;

use App\MiddlewareAbstract;
use App\Model\GroupRepo;
use App\Model\RepoException;
use App\Model\UserRepo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\HelperInterface;

/**
 * This actions loads the base data for the share part.
 *
 * @package App\Action
 */
class Share extends MiddlewareAbstract
{
    /**
     * The repository to load the available groups from.
     *
     * @var GroupRepo
     */
    private $groupRepo;

    /**
     * The repository to load the available users from.
     *
     * @var UserRepo
     */
    private $userRepo;

    /**
     * Creates the action with the required repositories.
     *
     * @param HelperInterface $helper
     * @param GroupRepo       $groupRepo
     * @param UserRepo        $userRepo
     */
    public function __construct(HelperInterface $helper, GroupRepo $groupRepo, UserRepo $userRepo)
    {
        parent::__construct($helper);
        $this->groupRepo = $groupRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Loads the groups and users available to share passwords to.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $currentUser = $request->getAttribute(Login::class);
            $groups = $this->groupRepo->getShareableGroups($currentUser);
            $users = $this->userRepo->getShareableUsers($currentUser);
            if (!$users && !$groups) {
                return $this->helper->create(200);
            }
            $data = [];
            if ($groups) {
                $data['groups'] = $groups;
            }
            if ($users) {
                $data['users'] = $users;
            }
            return $this->helper->create(200, $data);
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
    }
}
