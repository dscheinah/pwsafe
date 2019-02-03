<?php
use App\ApplicationProvider;
use Sx\Container\Injector;
use Sx\Server\ApplicationInterface;

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

/** @var ApplicationInterface $app */
$app = $injector->get(ApplicationInterface::class);
$app->run();
