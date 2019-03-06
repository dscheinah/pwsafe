<?php
use App\ApplicationProvider;
use Sx\Container\Injector;
use Sx\Message\ServerRequestFactory;
use Sx\Server\ApplicationInterface;
use Sx\Message\UriFactory;

$baseDirectory = dirname(__DIR__);
require $baseDirectory . '/vendor/autoload.php';

// Load configuration from the config dir. All files are merged in alphabetical order.
$options = [];
foreach (glob($baseDirectory . '/config/*.php') as $file) {
    $options[] = include $file;
}
$options = array_merge([], ...$options);

// Turn on error reporting if not in production environment according to configuration.
if (($options['env'] ?? false) !== 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Create the injection dependency container and fill it with definitions provided by the application.
$injector = new Injector($options);
$injector->setup(new ApplicationProvider());

// Create the server request to be handled by the application.
$uriFactory = new UriFactory();
$requestFactory = new ServerRequestFactory($uriFactory);
$request = $requestFactory->createServerRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER);

/** @var Sx\Server\ApplicationInterface $app */
$app = $injector->get(ApplicationInterface::class);
// Finally run the application. The app and all middleware are loaded by the injector.
// The middleware chain and routing options are provided with the used factories of the ApplicationProvider.
$app->run($request);
