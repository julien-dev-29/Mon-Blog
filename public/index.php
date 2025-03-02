<?php

require '../vendor/autoload.php';

use App\Blog\BlogModule;
use App\Framework\Renderer;
use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use function Http\Response\send;

$renderer = new Renderer();
$renderer->addPath(dirname(__DIR__) . '/' . '/templates');

$app = new App(
    modules: [
        BlogModule::class
    ],
    dependencies: [
        'renderer' => $renderer
    ]
);

$response = $app->run(request: ServerRequest::fromGlobals());

send($response);
