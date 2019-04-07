<?php
namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Action to delete a user.
 *
 * @package App\Action
 */
class UserDelete extends User
{
    /**
     * Deletes the user and on success returns the ID to client.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $id = (int)$request->getAttribute('id');
        // With the ID from the response the client will also delete the entry from the rendered data.
        if ($this->repo->deleteUser($id)) {
            return $this->helper->create(200, ['id' => $id]);
        }
        return $this->helper->create(501, ['message' => 'Der Benutzer konnte nicht gelöscht werden.']);
    }
}
