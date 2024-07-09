<?php

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/config');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../utils');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../constants');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../controllers');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../core/database');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../core/ServerMessages');

include_once('utils/Request.php');
include_once('utils/Response.php');
include_once('utils/Router.php');
include_once('controllers/UserController.php');
