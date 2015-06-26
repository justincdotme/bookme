<?php namespace bookMe\lib\Http;

interface RouterInterface {

    public function dispatch($method, $route, $controller, $action);

    public function catchError();
}