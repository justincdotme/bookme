<?php namespace bookMe\lib\Csrf;

use bookMe\lib\Http\Session;

/**
 * Class Csrf
 *
 * This class is responsible for checking that an HTTP request was issued from the authenticated user's machine.
 *
 * This class has 2 public methods: createToken and checkToken.
 * createToken generates a random token to be embedded in a hidden input field.
 * The checkToken method accepts 1 parameter $token (string).
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

class Csrf {

    /**
     * Create a new CSRF token.
     * Add token to session variable.
     *
     * @return string
     */
    public static function createToken()
    {
        $token = md5(uniqid(rand(), true));
        $sessionData = [
            'csrf_token' => $token
        ];
        Session::put($sessionData);
        return $token;
    }

    /**
     * Verify CSRF tokens match.
     *
     * @param $token
     * @return bool
     */
    public static function checkToken($token)
    {
        if(Session::get('csrf_token') !== $token)
        {
            return false;
        }
        return true;
    }
}