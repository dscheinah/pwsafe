<?php
namespace App\Action;

use App\Model\RepoException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Action to create or update passwords with the provided data.
 *
 * @package App\Action
 */
class PasswordSave extends Password
{
    /**
     * Stores the password data and proxies response generation to next action.
     * The next handler is expected to be the Password action.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Set user ID and key to encrypt provided data.
        $this->prepareRepo($request);
        // A successful save will return the ID of the updated which is given to the next handler to load data for it.
        try {
            return $handler->handle(
                $request->withAttribute('id', $this->repo->savePassword($request->getAttributes()))
            );
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
    }
}
