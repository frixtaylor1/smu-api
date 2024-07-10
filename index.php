<?php

declare(strict_types=1);

include_once('config/initialize.php');
include_once('utils/Request.php');
include_once('utils/Response.php');
include_once('utils/Router.php');
include_once('controllers/UserController.php');

$request  = new Request();
$response = new Response();

Router::dispatch($request, $response);
