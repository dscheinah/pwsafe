<?php
namespace App;

use App\Model\UserRepo;
use Sx\Container\Injector;
use App\Action\Login;
use Sx\Data\Session;
use Sx\Message\Response\HelperInterface;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Container\FactoryInterface;
use App\Action\Generate;

/**
 * The factory to create the Auth handler and register public routes.
 *
 * @package App
 */
class AuthFactory implements FactoryInterface
{
    /**
     * Creates the Auth handler with the session and registers public routes.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return Auth
     */
    public function create(Injector $injector, array $options, string $class): Auth
    {
        $auth = new Auth(
            $injector->get(MiddlewareHandlerInterface::class),
            $injector->get(Session::class),
            $injector->get(HelperInterface::class),
            $injector->get(UserRepo::class)
        );
        $auth->post('/generate', Generate::class);
        $auth->post('/login', Login::class);
        return $auth;
    }
}
