<?php
namespace App\Action;

use App\Model\RepoException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Action to query base data from all passwords.
 *
 * @package App\Action
 */
class PasswordList extends Password
{
    /**
     * Loads passwords from the database to be shown in a list view without details.
     * The response only contains data stored unencrypted to reduce loading time and response payload.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Sets the user ID (and the not needed key) to prevent loading passwords from other users.
        $this->prepareRepo($request);
        try {
            $term = $request->getAttribute('term', '');
            // Add the term inside each list entry to make it available to helpers used inside the render part.
            $passwords = array_map(
                function ($value) use ($term) {
                    $value['term'] = $term;
                    return $value;
                },
                $this->repo->getPasswords($term)
            );
            return $this->helper->create(200, ['list' => $passwords]);
        } catch (RepoException $e) {
            return $this->helper->create($e->getCode(), ['message' => $e->getMessage()]);
        }
    }
}
