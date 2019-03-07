<?php
namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Action to delete password.
 *
 * @package App\Action
 */
class PasswordDelete extends Password
{
    /**
     * Deletes the password for the provided ID.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Set user ID (and key, but not needed for delete) to the repository.
        $this->prepareRepo($request);
        $id = (int)$request->getAttribute('id');
        // Deletes the password for the given ID and user ID. If successful return the ID to the client.
        // With the ID from the response the client will also delete the entry from the rendered data.
        if ($this->repo->deletePassword($id)) {
            return $this->helper->create(200, ['id' => $id]);
        }
        return $this->helper->create(501, ['message' => 'Das Passwort konnte nicht gelöscht werden.']);
    }
}
