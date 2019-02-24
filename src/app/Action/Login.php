<?php
namespace App\Action;

use App\MiddlewareAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Sx\Message\Response\HelperInterface;
use Sx\Data\SessionInterface;

class Login extends MiddlewareAbstract
{

    private $session;

    public function __construct(HelperInterface $helper, SessionInterface $session)
    {
        parent::__construct($helper);
        $this->session = $session;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->session->set('login', true);
        return $this->helper->create(200, [
            'user' => $request->getAttribute('user'),
            'key' => uniqid('', false),
            'email' => uniqid('', false)
        ]);
    }
}
