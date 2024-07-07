<?php

declare(strict_types=1);

include_once('Request.php');
include_once('Response.php');
include_once('Router.php');

function userRouter()
{
    Router::get('/users', function (Request $request, Response $response) {
        $response
            ->setHeader('Content-type', 'application/json')
            ->setStatusCode(200)
            ->setBody([
                'message' => "Hello, World! from users!"
            ])->send();
    });

    Router::get('/users/{id}', function (Request $request, Response $response) {
        $response
            ->setHeader('Content-Type', 'application/json')
            ->setStatusCode(200)
            ->setBody([
                'sended_params' => json_encode($request->getParams())
            ])->send();
    });
}
