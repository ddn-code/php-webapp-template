<?php
if (!defined('__FOLDER_ROOT')) {
    define('__FOLDER_ROOT', dirname(__FILE__) . '/');
}

if (file_exists(__FOLDER_ROOT . 'config.local.php')) {
    require_once(__FOLDER_ROOT . 'config.local.php');
}

if (!defined('DEBUG')) {
    define('DEBUG', false);
}

if (!defined('__FOLDER_INC')) {
    define('__FOLDER_INC', __FOLDER_ROOT . 'inc/');
}

if (!defined('__FOLDER_RESOURCES')) {
    define('__FOLDER_RESOURCES', __FOLDER_ROOT . 'resources/');
}

if (!defined('__FOLDER_TEMPLATES')) {
    define('__FOLDER_TEMPLATES', __FOLDER_ROOT . 'templates/');
}

