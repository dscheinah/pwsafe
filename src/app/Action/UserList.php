<?php
namespace App\Action;

use App\Model\RepoException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The action to list all users for administration.
 *
 * @package App\Action
 */
class UserList extends User
{
    /**
     * Loads the basic data for all users.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $this->helper->create(200, ['list' => $this->repo->getUsers()]);
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
    }
}
