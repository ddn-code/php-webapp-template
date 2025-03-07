<?php
use ddn\api\Debug;
use ddn\api\router\Router;
use ddn\api\router\Renderer;
use ddn\api\router\Op;
use ddn\api\Helpers;

require_once('vendor/autoload.php');
require_once('config.php');

Debug::php_mode();

// Define the function so that we are able to use the next functions in the template, using the global scope
//  - add_js_file
//  - add_css_file
//  - dump_js_files
//  - dump_css_files
Helpers\Web::define_functions();

// First create the router for our application; it is a simple router that we must manage, and we'll set
//  the default folder to find the templates for the renderers. The router will attend to variable
//  $_OPERATION, which will contain the route that is being requested
$router = new Router("_ROUTE", __FOLDER_TEMPLATES);

if (defined('__CUSTOM_ROUTES') && (is_callable(__CUSTOM_ROUTES)))
    call_user_func_array(__CUSTOM_ROUTES, [ $router ]);

class NoOp extends Op {
    function _default_op() {
        print("Executing default operation");
    }
}

class ExampleOp extends Op {
    const _FUNCTIONS = [
        "op1" => "_op1",
        "op2" => [
            "" => "_op2_empty",
            "value1" => "_op2_value1",
        ]
    ];
    protected $_operation_result = null;
    function get_operation_result() {
        return $this->_operation_result;
    }
    function _op1() {
        echo "Executed operation 1";
        $this->_operation_result = "Operation 1";
    }
    function _op2_empty() {
        echo "Executed operation 2 with empty value";
        $this->_operation_result = "Operation 2 with empty value";
    }
    function _op2_value1() {
        echo "Executed operation 2 with value 1";
        $this->_operation_result = "Operation 2 with value 1";
    }
    function _default_op() {
        echo "Executing default operation for ExampleOp";
        $this->_operation_result = "Default operation";
    }
}

// Set the default renderer to use. 
// ** remember that variable $_VIEW will contain the result of the view for the operation once executed, so the 
//    should must use this variable to render the final result
// ** if no renderer is set, then the execution of the application will simply echo the result of the operation
//    i.e. will echo the content of $_VIEW
Renderer::set_default("renderer.pug");

// Add a route to the router. The route is a regular expression that will be matched against the path of the
//  request. If the route matches, then an object of the class NoOp (in this case) will be instantiated and
//  the renderer will also be set to the one specified in the third parameter
$router->add("/op", 'ExampleOp', 'op.pug');
$router->add("/", 'NoOp', 'index.pug');

// Execute the router, and get the operation that we must execute
$ops = $router->exec();

// If the router returns false, then there is no handler for the route, so we return a 404
if ($ops === false) {
    header("HTTP/1.0 404 Not Found");
    echo "Not found";
    exit;
}

// Render the operation
$ops->render();