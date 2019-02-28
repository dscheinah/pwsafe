<?php
namespace App\Action;

use App\MiddlewareAbstract;
use App\Model\UserRepo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sx\Data\SessionInterface;
use Sx\Message\Response\HelperInterface;

class Login extends MiddlewareAbstract
{

    protected $session;

    protected $repo;

    public function __construct(HelperInterface $helper, SessionInterface $session, UserRepo $repo)
    {
        parent::__construct($helper);
        $this->session = $session;
        $this->repo = $repo;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $request->getAttribute('user') ?: '';
        $password = $request->getAttribute('password') ?: '';
        if (! $this->repo->checkPassword($user, $password)) {
            return $this->helper->create(403, [
                'user' => $user,
                'message' => 'login data is invalid'
            ]);
        }
        $user = $this->repo->getUser($user, $password);
        $this->session->set(__CLASS__, $user['id']);
        unset($user['id'], $user['password']);
        return $this->helper->create(200, $user);
    }
}
