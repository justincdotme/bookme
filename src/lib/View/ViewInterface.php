<?php namespace bookMe\lib\View;

use stdClass;

interface ViewInterface {

    public function make($view, stdClass $data);
}