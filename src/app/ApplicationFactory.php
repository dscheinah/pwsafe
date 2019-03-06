<?php
namespace App;

use App\Handler\ErrorHandler;
use App\Handler\NotFoundHandler;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Server\Application;
use Sx\Server\ApplicationInterface;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Server\RouterInterface;

/**
 * This class creates the main dispatcher which needs to have all middleware handlers of the main chain attached.
 *
 * @package App
 */
class ApplicationFactory implements FactoryInterface
{
    /**
     * Creates the application and registers all first level middleware for this app.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return ApplicationInterface
     */
    public function create(Injector $injector, array $options, string $class): ApplicationInterface
    {
        // To create the handler chain the application needs a bridge between handler and middleware.
        $app = new Application($injector->get(MiddlewareHandlerInterface::class));
        // The error handler catches all exceptions and creates an error response from them.
        $app->add(ErrorHandler::class);
        // To handle login, public actions and authorization checks.
        $app->add(Auth::class);
        // The routing handler splits the chain according to the request path. The RouterFactory registers the actions.
        $app->add(RouterInterface::class);
        // Throws an explizit not found exception to be handled in the ErrorHandler.
        $app->add(NotFoundHandler::class);
        return $app;
    }
}
