<?php
namespace App\Action;

use App\MiddlewareAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Sx\Message\Response\HelperInterface;
use App\Model\PasswordRepo;

class Password extends MiddlewareAbstract
{

    protected $repo;

    public function __construct(HelperInterface $helper, PasswordRepo $repo)
    {
        parent::__construct($helper);
        $this->repo = $repo;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->prepareRepo($request);
        $password = $this->repo->getPassword($request->getAttribute('id'));
        if (! $password) {
            return $handler->handle($request);
        }
        return $this->helper->create(200, $password);
    }

    protected function prepareRepo(ServerRequestInterface $request): void
    {
        $this->repo->setUser($request->getAttribute(Login::class));
        $this->repo->setKey($request->getAttribute('key'));
    }
}
