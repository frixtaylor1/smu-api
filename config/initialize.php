<?php

/**
 * Presents the source directories for the includes...
 */
_setIncludePath('/config');
_setIncludePath('/../controllers');
_setIncludePath('/../constants');
_setIncludePath('/../models');
_setIncludePath('/../services');
_setIncludePath('/../core/Constants');
_setIncludePath('/../core/DataBaseHandler');
_setIncludePath('/../core/Router');
_setIncludePath('/../core/ServerMessages');
_setIncludePath('/../core/Validator');
_setIncludePath('/../core/ORM');

/**
 * --------------------------------------------------------------------
 * Pressenting Modules...
 * --------------------------------------------------------------------
 */
 // CORE BEGIN
include_once('Router.php');
include_once('Route.php');
include_once('Validator.php');
include_once('Request.php');
include_once('Response.php');
include_once('RouterConstants.php');
include_once('ValidatorConstants.php');
// @TODO: ORM && MIGRATION_MANAGER
include_once('DataBaseHandler.php');
include_once('Model.php');
include_once('DBTypesConstants.php');
// CORE END

/**
 * --------------------------------------------------------------------
 * Pressenting Controllers...
 * --------------------------------------------------------------------
 */
include_once('UserController.php');

/**
 * --------------------------------------------------------------------
 * Pressenting Services...
 * --------------------------------------------------------------------
 */
include_once('UserService.php');

 
 /**
 * --------------------------------------------------------------------
 * Pressenting Models...
 * --------------------------------------------------------------------
 */
include_once('UserModel.php');


 /**
 * --------------------------------------------------------------------
 * Pressenting Constants...
 * --------------------------------------------------------------------
 */
include_once('UserConstants.php');

/**
 * --------------------------------------------------------------------
 * Private funcitons...
 * --------------------------------------------------------------------
 */
function _setIncludePath(string $path) {
    set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . $path);
}
