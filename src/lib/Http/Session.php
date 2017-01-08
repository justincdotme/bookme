<?php namespace bookMe\lib\Http;

/**
 * Class Session
 *
 * This class is responsible for handling HTTP sessions.
 *
 * The Session class consists of static helper methods used to start or destroy a session and manage session data.
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

class Session implements SessionInterface {

    public static function start()
    {
        //Reduce likelihood of XSS attack
        ini_set('session.cookie_httponly', 1);
        //Keep session ID in a cookie, not URL
        ini_set('session.use_only_cookies', 1);
        session_name('bookMe-Session');
        session_start();
        //Mitigate session fixation attacks.
        session_regenerate_id();
    }

    /**
     * Access a session variable.
     *
     * @param $key
     * @return mixed
     */
    public static function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }

    /**
     * Access a session variable one time, then remove it.
     *
     * @param $key
     * @return mixed
     */
    public static function pull($key)
    {
        if(isset($_SESSION[$key]))
        {
            $data = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $data;
        }
        return false;
    }

    /**
     * Determine if session variable is set.
     *
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Place a key/value pair in the session data.
     *
     * @param array $data
     * @return mixed
     */
    public static function put(array $data)
    {
        foreach($data as $key => $value)
        {
            $_SESSION[$key] = $value;
        }
        return true;
    }

    /**
     * Kill an active session.
     *
     */
    public static function destroy()
    {
        session_unset();
    }
}