<?php

require dirname(path: __DIR__) . '/vendor/autoload.php';

use App\Blog\BlogModule;
use DI\ContainerBuilder;
use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use function Http\Response\send;

$modules = [
    BlogModule::class
];

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');
foreach ($modules as $module) {
    if ($module::DEFINITIONS !== null) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}
$builder->addDefinitions(dirname(__DIR__) . '/config.php');

$container = $builder->build();

$app = new App(
    container: $container,
    modules: $modules
);

if (php_sapi_name() !== "cli") {
    $response = $app->run(request: ServerRequest::fromGlobals());
    send($response);
}
