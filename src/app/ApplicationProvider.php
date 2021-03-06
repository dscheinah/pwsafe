<?php
namespace App;

use App\Action\Category;
use App\Action\CategoryDelete;
use App\Action\CategoryFactory;
use App\Action\CategoryList;
use App\Action\CategorySave;
use App\Action\Generate;
use App\Action\GenerateFactory;
use App\Action\Group;
use App\Action\GroupDelete;
use App\Action\GroupFactory;
use App\Action\GroupList;
use App\Action\GroupSave;
use App\Action\Login;
use App\Action\LoginFactory;
use App\Action\Password;
use App\Action\PasswordDelete;
use App\Action\PasswordFactory;
use App\Action\PasswordList;
use App\Action\PasswordSave;
use App\Action\ProfileFactory;
use App\Action\ProfileSave;
use App\Action\Share;
use App\Action\ShareFactory;
use App\Action\User;
use App\Action\UserDelete;
use App\Action\UserFactory;
use App\Action\UserList;
use App\Action\UserSave;
use App\Handler\ErrorHandler;
use App\Handler\ErrorHandlerFactory;
use App\Handler\NotFoundHandler;
use App\Model\CategoryRepo;
use App\Model\CategoryRepoFactory;
use App\Model\CategoryStorage;
use App\Model\GroupRepo;
use App\Model\GroupRepoFactory;
use App\Model\GroupStorage;
use App\Model\PasswordRepo;
use App\Model\PasswordRepoFactory;
use App\Model\PasswordStorage;
use App\Model\PasswordStorageFactory;
use App\Model\UserRepo;
use App\Model\UserRepoFactory;
use App\Model\UserStorage;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Sx\Container\Injector;
use Sx\Container\ProviderInterface;
use Sx\Data\Backend\MySqlBackendFactory;
use Sx\Data\BackendInterface;
use Sx\Data\Session;
use Sx\Data\StorageFactory;
use Sx\Message\Response\HelperInterface;
use Sx\Message\Response\JsonFactory;
use Sx\Message\ResponseFactory;
use Sx\Message\StreamFactory;
use Sx\Server\ApplicationInterface;
use Sx\Server\MiddlewareHandlerFactory;
use Sx\Server\MiddlewareHandlerInterface;
use Sx\Server\RouterInterface;
use Sx\Utility\ErrorLog;
use Sx\Utility\LogInterface;

/**
 * A configuration provider for the dependency injection container.
 *
 * @package App
 */
class ApplicationProvider implements ProviderInterface
{
    /**
     * Registers the factories for all classes used in this application. It is called before all other initialization.
     *
     * @param Injector $injector
     */
    public function provide(Injector $injector): void
    {
        $injector->set(ApplicationInterface::class, ApplicationFactory::class);
        $injector->set(Auth::class, AuthFactory::class);
        $injector->set(BackendInterface::class, MySqlBackendFactory::class);
        $injector->set(Category::class, CategoryFactory::class);
        $injector->set(CategoryDelete::class, CategoryFactory::class);
        $injector->set(CategoryList::class, CategoryFactory::class);
        $injector->set(CategoryRepo::class, CategoryRepoFactory::class);
        $injector->set(CategorySave::class, CategoryFactory::class);
        $injector->set(CategoryStorage::class, StorageFactory::class);
        $injector->set(ErrorHandler::class, ErrorHandlerFactory::class);
        $injector->set(Group::class, GroupFactory::class);
        $injector->set(GroupDelete::class, GroupFactory::class);
        $injector->set(GroupList::class, GroupFactory::class);
        $injector->set(GroupRepo::class, GroupRepoFactory::class);
        $injector->set(GroupSave::class, GroupFactory::class);
        $injector->set(GroupStorage::class, StorageFactory::class);
        $injector->set(LogInterface::class, ErrorLog::class);
        $injector->set(Generate::class, GenerateFactory::class);
        $injector->set(HelperInterface::class, JsonFactory::class);
        $injector->set(Login::class, LoginFactory::class);
        $injector->set(MiddlewareHandlerInterface::class, MiddlewareHandlerFactory::class);
        $injector->set(NotFoundHandler::class, MiddlewareFactory::class);
        $injector->set(Password::class, PasswordFactory::class);
        $injector->set(PasswordDelete::class, PasswordFactory::class);
        $injector->set(PasswordList::class, PasswordFactory::class);
        $injector->set(PasswordRepo::class, PasswordRepoFactory::class);
        $injector->set(PasswordSave::class, PasswordFactory::class);
        $injector->set(PasswordStorage::class, PasswordStorageFactory::class);
        $injector->set(ProfileSave::class, ProfileFactory::class);
        $injector->set(ResponseFactoryInterface::class, ResponseFactory::class);
        $injector->set(RouterInterface::class, RouterFactory::class);
        $injector->set(Session::class, SessionFactory::class);
        $injector->set(Share::class, ShareFactory::class);
        $injector->set(StreamFactoryInterface::class, StreamFactory::class);
        $injector->set(User::class, UserFactory::class);
        $injector->set(UserDelete::class, UserFactory::class);
        $injector->set(UserList::class, UserFactory::class);
        $injector->set(UserRepo::class, UserRepoFactory::class);
        $injector->set(UserSave::class, UserFactory::class);
        $injector->set(UserStorage::class, StorageFactory::class);
    }
}
