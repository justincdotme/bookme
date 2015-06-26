<?php namespace bookMe\lib\Auth;

interface AuthInterface {

    /**
     * @param array $credentials
     * @return bool
     */
    public function login(array $credentials);

    /**
     * @return bool
     */
    public function checkLoggedIn();


    /**
     * @return bool
     */
    public function logout();

}