<?php namespace bookMe\lib\Email;

use stdClass;

interface EmailInterface {

    /**
     * @param stdClass $email
     * @return mixed
     */
    public function send(stdClass $email);
}