<?php
use App\ApplicationProvider;
use Sx\Container\Injector;
use Sx\Message\RequestFactory;
use Sx\Message\ServerRequestFactory;
use Sx\Server\ApplicationInterface;
use Sx\Message\UriFactory;

require dirname(__DIR__) . '/vendor/autoload.php';

$options = [];
foreach (glob(dirname(__DIR__) . '/config/*.php') as $file) {
    $options[] = include $file;
}
$options = array_merge([], ...$options);

if (($options['env'] ?? false) !== 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

$injector = new Injector($options);
$injector->setup(new ApplicationProvider());

$uriFactory = new UriFactory();
$requestFactory = new ServerRequestFactory($uriFactory);
$request = $requestFactory->createServerRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER);

/** @var Sx\Server\ApplicationInterface $app */
$app = $injector->get(ApplicationInterface::class);
$app->run($request);
