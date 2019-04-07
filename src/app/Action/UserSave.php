<?php
namespace App\Action;

use App\Model\RepoException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * The action to save user data for administration.
 *
 * @package App\Action
 */
class UserSave extends User
{
    /**
     * Saves the user data.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->getAttribute('user')) {
            return $this->helper->create(422, ['message' => 'Es muss ein Benutzer angegeben werden.']);
        }
        // A successful save will return the ID of the updated which is given to the next handler to load data for it.
        try {
            return $handler->handle(
                $request->withAttribute('id', $this->repo->saveUser($request->getAttributes()))
            );
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
    }
}
