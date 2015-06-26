<?php namespace bookMe\lib\Http;

interface SessionInterface {

    public static function start();

    public static function get($key);

    public static function pull($key);

    public static function has($key);

    public static function put(array $data);

    public static function destroy();
}