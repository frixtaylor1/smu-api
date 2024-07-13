<?php

declare(strict_types=1);

include_once('config/initialize.php');

use SMU\Core\Request;
use SMU\Core\Response;
use SMU\Core\Router;

$request  = new Request();
$response = new Response();

Router::dispatch($request, $response);
