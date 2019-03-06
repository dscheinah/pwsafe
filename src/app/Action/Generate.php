<?php
namespace App\Action;

use App\MiddlewareAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Action to create a cryptographically secure password for the client.
 *
 * @package App\Action
 */
class Generate extends MiddlewareAbstract
{
    /**
     * Creates a random password with printable characters according to the given settings form generation form.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws \RuntimeException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $targetLength = $request->getAttribute('length', 20);
        $password = '';
        $length = 0;
        // Fill the password to the required length.
        // This needs to loop since char filters are applied to the random byte creation each run.
        while ($length < $targetLength) {
            try {
                // Create random bytes and filter it to printable chars.
                $password .= preg_replace('/[^\x21-\x7E]/', '', random_bytes($targetLength - $length));
            } catch (\Exception $e) {
                throw new \RuntimeException('failed to create random password: ' . $e->getMessage(), $e->getCode(), $e);
            }
            $length = \strlen($password);
        }
        return $this->helper->create(
            200,
            [
                'password' => $password,
            ]
        );
    }
}
