<?php

/**
 * Presents the source directories for the includes...
 */
_setIncludePath('/config');
_setIncludePath('/../utils');
_setIncludePath('/../constants');
_setIncludePath('/../controllers');
_setIncludePath('/../core/database');
_setIncludePath('/../core/ServerMessages');
_setIncludePath('/../services');

/**
 * --------------------------------------------------------------------
 * Private funcitons...
 * --------------------------------------------------------------------
 */
function _setIncludePath(string $path) {
    set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . $path);
}
