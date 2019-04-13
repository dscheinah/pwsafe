<?php
namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Action to delete a group.
 *
 * @package App\Action
 */
class GroupDelete extends Group
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
        $id = (int)$request->getAttribute('id');
        // Deletes the group for the given ID. If successful return the ID to the client.
        // With the ID from the response the client will also delete the entry from the rendered data.
        if ($this->repo->deleteGroup($id)) {
            return $this->helper->create(200, ['id' => $id]);
        }
        return $this->helper->create(501, ['message' => 'Die Gruppe konnte nicht gelöscht werden.']);
    }
}
