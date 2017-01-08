<?php namespace bookMe\lib\Auth;

use bookMe\lib\Http\Session;
use bookMe\Model\User;
use Exception;

/**
 * Class Auth
 *
 * This class is responsible for authenticating users.
 *
 * This class has 1 dependency: an instance of the User model.
 * The Auth class contains methods for authenticating registered users.
 * The Auth class also contains helper methods for working with authenticated users.
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
 * @link https://bookme.justinc.me
 *
 */

class Auth implements AuthInterface {

    protected $_user;
    protected $_errors;

    public function __construct(User $user)
    {
        $this->_user = $user;
        $this->_errors = [];
    }

    /**
     * Authenticate a user.
     *
     * @return bool
     */
    public function login(array $credentials)
    {
        try {
            $user = $this->_user->getUserByEmail($credentials['email']);
        } catch(Exception $e) {
            return false;
        }

        if(!$user)
        {
            return false;
        }

        $authenticated = password_verify($credentials['password'], $user->password);

        if(!$authenticated)
        {
            return false;
        }

        //Log the user into the application.
        $userSessionData = [
            'id' => $user->uid,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'logged_in' => true
        ];

        Session::put($userSessionData);

        return header('Location: ' . POST_LOGIN_URL);
    }

    /**
     * Check if the user is authenticated.
     *
     * @return bool
     */
    public function checkLoggedIn()
    {
        return Session::get('logged_in');
    }

    /**
     * Redirect unauthenticated user to the login page.
     *
     * @return bool|void
     */
    public function redirectIfNotAuthenticated()
    {
        if(!$this->checkLoggedIn())
        {
            return header('Location: ' . LOGIN_URL);
        }
        return true;
    }

    /**
     * Log the user out of the application.
     *
     * @return bool
     */
    public function logout()
    {
        Session::destroy();
        header('Location: ' . POST_LOGOUT_URL);
        return true;
    }

    /**
     * Hash the supplied password.
     *
     * @param $password
     * @return bool|string
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return Session::get('id') === 1;
    }

    /**
     * Get the ID of the authenticated user.
     *
     * @return mixed
     */
    public function getAuthenticatedUser()
    {
        return Session::get('id');
    }
}