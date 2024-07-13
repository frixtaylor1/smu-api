<?php

/**
 * Presents the source directories for the includes...
 */
_setIncludePath('/config');
_setIncludePath('/../controllers');
_setIncludePath('/../services');
_setIncludePath('/../core/Constants');
_setIncludePath('/../core/DataBaseHandler');
_setIncludePath('/../core/Router');
_setIncludePath('/../core/ServerMessages');
_setIncludePath('/../core/Validator');

/**
 * --------------------------------------------------------------------
 * Pressenting Modules...
 * --------------------------------------------------------------------
 */
include_once('Router.php');
include_once('Route.php');
include_once('DataBaseHandler.php');
include_once('Validator.php');
include_once('Request.php');
include_once('Response.php');
include_once('RouterConstants.php');
include_once('ValidatorConstants.php');

/**
 * --------------------------------------------------------------------
 * Pressenting Controllers...
 * --------------------------------------------------------------------
 */
include_once('UserController.php');

/**
 * --------------------------------------------------------------------
 * Private funcitons...
 * --------------------------------------------------------------------
 */
function _setIncludePath(string $path) {
    set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . $path);
}
