<?php
namespace App\Action;

use App\Model\RepoException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * An action to handle insert and update of a category.
 *
 * @package App\Action
 */
class CategorySave extends Category
{
    /**
     * Validates and saves data for a category.
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
        // Set the user ID.
        $this->prepareRepo($request);
        // A successful save will return the ID of the updated which is given to the next handler to load data for it.
        try {
            return $handler->handle(
                $request->withAttribute('id', $this->repo->saveCategory($request->getAttributes()))
            );
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
    }
}
