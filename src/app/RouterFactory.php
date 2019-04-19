<?php
namespace App;

use App\Action\Category;
use App\Action\CategoryDelete;
use App\Action\CategoryList;
use App\Action\CategorySave;
use App\Action\Group;
use App\Action\GroupDelete;
use App\Action\GroupList;
use App\Action\GroupSave;
use App\Action\Password;
use App\Action\PasswordList;
use App\Action\PasswordSave;
use App\Action\Share;
use App\Action\User;
use App\Action\UserDelete;
use App\Action\UserList;
use App\Action\UserSave;
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
        $router->get('/category', Category::class);
        $router->get('/categories', CategoryList::class);
        $router->get('/group', Group::class);
        $router->get('/groups', GroupList::class);
        $router->get('/password', Password::class);
        $router->get('/passwords', PasswordList::class);
        $router->get('/share', Share::class);
        $router->get('/user', User::class);
        $router->get('/users', UserList::class);
        // Routes to save data. After successful save the data will be loaded using the chained load action.
        $router->post('/category', CategorySave::class);
        $router->post('/category', Category::class);
        $router->post('/group', GroupSave::class);
        $router->post('/group', Group::class);
        $router->post('/password', PasswordSave::class);
        $router->post('/password', Password::class);
        $router->post('/profile', ProfileSave::class);
        $router->post('/user', UserSave::class);
        $router->post('/user', User::class);
        // Search for passwords.
        $router->post('/passwords', PasswordList::class);
        // Routes to delete.
        $router->delete('/category', CategoryDelete::class);
        $router->delete('/group', GroupDelete::class);
        $router->delete('/password', PasswordDelete::class);
        $router->delete('/user', UserDelete::class);
        return $router;
    }
}
