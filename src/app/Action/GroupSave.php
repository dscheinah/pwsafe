<?php
namespace App\Action;

use App\Model\RepoException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * An action to handle insert and update of a group.
 *
 * @package App\Action
 */
class GroupSave extends Group
{
    /**
     * Validates and saves data for a group.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->getAttribute('name')) {
            return $this->helper->create(422, ['message' => 'Es muss ein Name angegeben werden.']);
        }
        // A successful save will return the ID of the updated which is given to the next handler to load data for it.
        try {
            return $handler->handle(
                $request->withAttribute('id', $this->repo->saveGroup($request->getAttributes()))
            );
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
    }
}
