<?php namespace bookMe\lib\Validation;

interface ValidatorInterface {

    public function validate(array $rules, array $postData);

    public function getErrors();
}