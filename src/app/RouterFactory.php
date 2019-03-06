<?php
namespace App;

use App\Action\Password;
use App\Action\PasswordList;
use App\Action\PasswordSave;
use Sx\Container\FactoryInterface;
use Sx\Container\Injector;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Server\Router;
use Sx\Server\RouterInterface;
use App\Action\PasswordDelete;
use App\Action\ProfileSave;

/**
 * Factory to configure the app specific router with its middleware tree chains.
 *
 * @package App
 */
class RouterFactory implements FactoryInterface
{
    /**
     * Creates the router and registers middleware action for all defined routes.
     *
     * @param Injector $injector
     * @param array    $options
     * @param string   $class
     *
     * @return RouterInterface
     */
    public function create(Injector $injector, array $options, string $class): RouterInterface
    {
        $router = new Router($injector->get(MiddlewareHandlerInterface::class));
        // Routes to load data.
        $router->get('/password', Password::class);
        $router->get('/passwords', PasswordList::class);
        // Routes to save data. After successful password save the data will be loaded using the chained load action.
        $router->post('/password', PasswordSave::class);
        $router->post('/password', Password::class);
        $router->post('/profile', ProfileSave::class);
        // Routes to delete.
        $router->delete('/password', PasswordDelete::class);
        return $router;
    }
}
