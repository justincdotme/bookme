<?php namespace bookMe\lib\Http;

interface RequestInterface {

    public function getMethod();

    public function getUri();

    public function getInput($field);

    public function getRawInput($field);

    public function getUploadedFile($file);

    public function getRawPostInput();

    public function getAllPostInput();

}