# App template

This is a template to create a web app using ddn/router (more information about the utilities in https://github.com/ddn-code/webrouter)

## How this works

`ddn/router` helps to create web applications in php. The idea is that the framework receives the route to access in a configurable variable (which defaults to `_ROUTE`). And once the server is accessed from the browser, it checks the routes that is going to serve, creates an object (of class `Op`) that executes the actions required for the operation. And then generates a view (which will be generated in variable `$_VIEW`).

`ddn/router` relies on a `_GET` variable (defined in constant `__ROUTE_VAR` in the example, which defaults to `_ROUTE`), and to make it work, it is provided a `.htaccess` file that rewrites the accesses to the application to put the route in the variable.

As an example: `https://my.server/my/cool/operation` will be rewritten to `https://my.server/index.php?_ROUTE=my/cool/operation`.

When `.htaccess` is not available, it is provided an `htaccess.php` file that tries to mimic this behavior.

> Please adjust `.htaccess` file to meet the same variable in `__ROUTE_VAR` (if changed).

### Web application model

A web application consists of:

- A set of _endpoints_ that define paths in the web application that will be served.
- A set of operations (i.e. classes derived from `Op` class) that implement the actions at an _endpoint_ (e.g. creating or deleting an object, log-in a user, etc.).
- A set of _views_, that show the result of the execution of an operation.
- A set of _renderers_, that show the views.

Detaching the _renderers_ from the _views_ makes it easy to create an homogeneous web application. The _renderer_ can be understood as a _layout_ for the web application, and the _view_ is estrictly the output of a web operation.

If it is not needed this kind of distinction, it is possible to render each _view_ as a whole output and ignore the _renderer_ (i.e. define the renderer as `($view) => { echo $view; }`).

At the end:
- A _view_ is an html fragment that shows the output of an operation.
- A _renderer_ is a html layout in which a view is placed.

#### Workflow

When a request arrives to the web server
1. The router evaluates whether there is a route defined or not
2. If there is no route that matches the URI, finalize.
3. Retrieve the parameters from the URI and instantiate the `Op` class that implements the operation.
4. Call funcion `_do` from `Op`
5. `_do` will chech whether there is a sub-operation defined; if not, it will call `_do_default_operation`. If there is a sub-operation, it will be executed.
6. Generate the view for the operation
    - `pug files`: var `_OP_HANDLER` will be available.
    - other file or function: global var `$_OP_HANDLER` will be available
7. Render the final layout:
    - `pug files`: vars `_OP_HANDLER` and `_VIEW` will be available.
    - other file or function: global vars `$_OP_HANDLER` and `$_VIEW` will be available

(*) a view will not be generated unless the result of the `exec()` call is rendered (i.e. method `render()` is called).

### The Op class

I think that it is better shown by this example:

```php
class OpUser extends ddn\api\Router\Op {
    const _FUNCTIONS = [
        "login" => "_login",
        "logout" => "_logout",
        "update" => [
            "" => "_update_data",
            "password" => "_update_password",
        ]
    ];

    function _login() {
        echo "executed the login op";
    }
    function _logout() {
        echo "executed the logout op";
    }
    function _update_data() {
        $_POST["name"] = $_POST["name"]??null;
        $_POST["email"] = $_POST["email"]??null;
        echo "would set:<br>\n- user to {$_POST['name']}<br>\n- email to {$_POST['email']}<br>";
        echo "executed the update data op<br>";
    }
    function _update_password() {
        echo "executed the update password op";
    }
    function _default_op() {
        echo "executed the default op";
    }
}
```

- if the variable `login` is set, it will execute the function `_login`
- if the variable `logout` is set, it will execute the function `_logout`
- if the variable `update` is set to _empty string_, it will execute the function `_update_data`
- if the variable `update` is set to _password_, it will execute the function `_update_password`
- if none of these variables are set, it will execute the function `_default_op`.

## Dependencies

The application depends on ddn/router and ddn/common. The dependencies are included in composer.json.

## Using

Just get into the main folder install the application using composer:

```bash
$ composer install
```

Then you can start a web server using php to test the web application:

```bash
$ php -S localhost:8001 htaccess.php
```

> `htaccess.php` is a script that tries to somehow _copy_ the behavior of file `.htaccess` in the example.
