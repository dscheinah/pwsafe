<?php
namespace App\Action;

use App\MiddlewareAbstract;
use App\Model\UserRepo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Message\Response\HelperInterface;

class ProfileSave extends MiddlewareAbstract
{

    protected $repo;

    public function __construct(HelperInterface $helper, UserRepo $repo)
    {
        parent::__construct($helper);
        $this->repo = $repo;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->repo->saveUser($request->getAttribute(Login::class), $request->getAttributes());
        if (! $user) {
            return $handler->handle($request);
        }
        unset($user['id'], $user['password']);
        return $this->helper->create(200, $user);
    }
}
