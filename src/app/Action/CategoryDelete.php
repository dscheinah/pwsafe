<?php
namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Action to delete a category.
 *
 * @package App\Action
 */
class CategoryDelete extends Category
{
    /**
     * Checks if the category can be deleted and if yes deletes it.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Set the user ID to the repository.
        $this->prepareRepo($request);
        $id = (int)$request->getAttribute('id');
        // Deletes the password for the given ID and user ID. If successful return the ID to the client.
        // With the ID from the response the client will also delete the entry from the rendered data.
        if ($this->repo->deleteCategory($id)) {
            return $this->helper->create(200, ['id' => $id]);
        }
        return $this->helper->create(501, ['message' => 'Die Kategorie konnte nicht gelöscht werden.']);
    }
}
