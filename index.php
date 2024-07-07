<?php

declare(strict_types=1);

include_once('config/initialize.php');

$request  = new Request();
$response = new Response();

userRouter();

Router::dispatch($request, $response);
