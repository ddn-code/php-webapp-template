<?php
// Use this snippet to serve static files from the same directory as router.php, when .htaccess file is not
// available or not allowed by the server, or when using php built-in server.
// 
// This snippet is based on the following answer from stackoverflow:
// https://stackoverflow.com/a/38926070/14699733
//
// To use this snippet, name it as (e.g.) router.php and place it in the same directory as index.php.
//
// Then start the server with the following command:
// php -S localhost:8000 router.php
//
// Then whenever a request is made to the server, the router.php file will be executed.
// If the requested file exists, it will be served. Otherwise, the file index.php will be served, and the
// requested file will be passed as a parameter to index.php in variable _OPERATION_VAR (which defaults to
// _ROUTE).

if (!defined('__ROUTE_VAR')) {
    define('__ROUTE_VAR', '_ROUTE');
}

chdir(__DIR__);
$filePath = realpath(ltrim($_SERVER["REQUEST_URI"], '/'));
if ($filePath && is_dir($filePath)){
    // attempt to find an index file
    foreach (['index.php', 'index.html'] as $indexFile){
        if ($filePath = realpath($filePath . DIRECTORY_SEPARATOR . $indexFile)){
            break;
        }
    }
}
if ($filePath && is_file($filePath)) {
    // 1. check that file is not outside of this directory for security
    // 2. check for circular reference to router.php
    // 3. don't serve dotfiles
    if (strpos($filePath, __DIR__ . DIRECTORY_SEPARATOR) === 0 &&
        $filePath != __DIR__ . DIRECTORY_SEPARATOR . basename(__FILE__) &&
        substr(basename($filePath), 0, 1) != '.'
    ) {
        if (strtolower(substr($filePath, -4)) == '.php') {
            // php file; serve through interpreter
            include $filePath;
        } else {
            // asset file; serve from filesystem
            return false;
        }
    } else {
        // disallowed file
        header("HTTP/1.1 404 Not Found");
        echo "404 Not Found";
    }
} else {
    // rewrite to our index file
    $_GET[__ROUTE_VAR] = ltrim($_SERVER["REQUEST_URI"], "/");
    include __DIR__ . DIRECTORY_SEPARATOR . 'index.php';
}