<?php namespace bookMe\lib\Http;

/**
 * Class Router
 *
 * This class is responsible for routing incoming HTTP requests.
 * This class has 1 dependency, an instance of the Request class.
 *
 * This class contains 2 public methods: dispatch and catchError.
 * The dispatch method accepts 4 parameters: $method, $route, $controller and $action
 * The catchError method is called after the dispatch method and is used to handle 404 errors.
 *
 *
 *
 * PHP Version 5.6
 *
 * License: Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package bookMe
 * @author Justin Christenson <info@justinc.me>
 * @version 1.0.0
 * @license http://opensource.org/licenses/mit-license.php
 * @link http://bookme.demos.justinc.me
 *
 */

class Router implements RouterInterface {

    protected $_controller;
    protected $_request;
    protected $_routes;
    protected $_route;
    protected $_uri;
    protected $_foundRoute;
    protected $_routeParameter;

    public function __construct(Request $request)
    {
        $this->_controller = '\bookMe\Controller\\';
        $this->_request = $request;
        $this->_route = $this->_request->getUri();
        $this->getRouteParams();
        $this->_foundRoute = false;
    }

    /**
     * Respond to incoming route requests.
     * Check route, method and controller exist.
     * Instantiate specified controller, call method.
     *
     * @param $method
     * @param $route
     * @param $controller
     * @param $action
     * @return bool|int|void
     */
    public function dispatch($method, $route, $controller, $action)
    {
        $method = strtolower($method);

        //Prevent duplicate requests to same route for different methods.
        if($this->_foundRoute)
        {
            return;
        }

        //If the current uri is not the current route being checked.
        if($this->_route !== $route)
        {
            return;
        }

        $method = strtolower($method);
        if($method !== $this->_request->getMethod())
        {
            return;
        }

        if(!$this->checkController($controller))
        {
            return http_response_code(500);
        }
        $this->_foundRoute = true;

        $this->_controller .= $controller;
        $controller = new $this->_controller;

        if(!method_exists($controller, $action))
        {
            return http_response_code(500);
        }
        if(!is_null($this->_routeParameter))
        {
            $controller->$action($this->_routeParameter);
            return true;
        }
        $controller->$action();
        return true;
    }

    /**
     * Check if a controller exists for the requested route.
     *
     * @param $controller
     * @return bool
     */
    protected function checkController($controller)
    {
        $controllerClass = explode('\\', $controller);
        $controllerClass = '../src/Controller/' . end($controllerClass) . '.php';
        if(!file_exists($controllerClass))
        {
            return false;
        }
        return true;
    }

    /**
     * Split the request URI into an array.
     *
     * @return array
     */
    protected function splitRouteParts()
    {
        return explode('/', $this->_request->getUri());
    }

    /**
     * Extract the route parameters from the route.
     * Assign the route parameters to a protected variable.
     * Reassemble the route as it reads in routes.php to avoid a 404.
     *
     * @return bool
     */
    protected function getRouteParams()
    {
        $routeParts = $this->splitRouteParts();
        $parameter = end($routeParts);
        if(is_numeric($parameter))
        {
            $this->_routeParameter = intval($parameter);
            array_pop($routeParts);
            $routeParts[] = '{id}';
            $this->_route = implode('/', $routeParts);
            return true;
        }
        return false;
    }

    /**
     * Handle 404 errors.
     * This method is the last method called in the routes.php file.
     *
     */
    public function catchError()
    {
        if(!$this->_foundRoute)
        {
            return header("Location: " . SITE_URL);
        }
    }
}