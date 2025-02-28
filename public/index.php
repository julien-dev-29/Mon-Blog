<?php

require '../vendor/autoload.php';

use App\Blog\BlogModule;
use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use function Http\Response\send;

$app = new App(
    modules: [
        BlogModule::class
    ]
);

$response = $app->run(request: ServerRequest::fromGlobals());

send($response);
