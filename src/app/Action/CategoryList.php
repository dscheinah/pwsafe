<?php
namespace App\Action;

use App\Model\RepoException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Action to query all categories available to the users. These will be used in multiple templates.
 *
 * @package App\Action
 */
class CategoryList extends Category
{
    /**
     * Loads all categories and returns them to the client.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Sets the user ID to prevent loading categories from other users.
        $this->prepareRepo($request);
        try {
            return $this->helper->create(200, ['list' => $this->repo->getCategories()]);
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
    }
}
