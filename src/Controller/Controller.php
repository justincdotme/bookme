<?php namespace bookMe\Controller;

use bookMe\lib\Auth\Auth;
use bookMe\lib\Http\Request;
use bookMe\lib\View\View;
use bookMe\Model\User;

/**
 * Class Controller
 *
 * This is a base class for the application's Controllers.
 *
 * This class handles instantiation of the User model as well as the View, Request and Auth objects.
 * These classes are used by the application's Controllers.
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

abstract class Controller {

    protected $_user;
    protected $_view;
    protected $_request;
    protected $_auth;

    public function __construct()
    {
        $this->_user = new User;
        $this->_view = new View;
        $this->_request = new Request;
        $this->_auth = new Auth($this->_user);
    }
}