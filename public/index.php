<?php

require '../vendor/autoload.php';

use App\Blog\BlogModule;
use Framework\App;
use Framework\Renderer\PHPRenderer;
use Framework\Renderer\TwigRenderer;
use GuzzleHttp\Psr7\ServerRequest;
use function Http\Response\send;

$renderer = new TwigRenderer(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates');

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
